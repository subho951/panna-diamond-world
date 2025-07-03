<?php
namespace App\Http\Controllers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Helpers\Helper;
class TableController extends Controller
{
    public function fetch(Request $request)
    {
        // Helper::pr($request->all());
        $table = $request->input('table');
        $orderBy = $request->input('orderBy', 'id');
        $orderType = $request->input('orderType', 'desc');
        $rawColumns = explode(',', $request->input('columns'));
        $search = $request->input('search');
        $page = $request->input('page', 1);
        // $limit = 50;
        $limit = $request->input('perPage', 50); // Default to 50

        if (!in_array('id', $rawColumns)) {
            $rawColumns[] = 'id';
        }

        // Start query
        $query = DB::table($table);

        // JOINs (PostgreSQL-safe with CAST)
        if ($table === 'states') {
            $query->leftJoin('countries', DB::raw("$table.country_id"), '=', DB::raw("countries.id"));
        }
        if ($table === 'cities') {
            $query->leftJoin('countries', DB::raw("$table.country_id"), '=', DB::raw("countries.id"));
            $query->leftJoin('states', DB::raw("$table.state_id"), '=', DB::raw("states.id"));
        }
        if ($table === 'campaigns') {
            $query->leftJoin('campaign_types', DB::raw("$table.campaign_type_id"), '=', DB::raw("campaign_types.id"));
        }
        if ($table === 'faq_sub_categories') {
            $query->leftJoin('faq_categories', DB::raw("$table.faq_category_id"), '=', DB::raw("faq_categories.id"));
        }
        if ($table === 'faqs') {
            $query->leftJoin('faq_categories', DB::raw("$table.faq_category_id"), '=', DB::raw("faq_categories.id"));
            $query->leftJoin('faq_sub_categories', DB::raw("$table.faq_sub_category_id"), '=', DB::raw("faq_sub_categories.id"));
        }
        if ($table === 'users') {
            $query->leftJoin('roles', DB::raw("$table.role_id"), '=', DB::raw("roles.id"));
            $query->leftJoin('branches', DB::raw("$table.branch_id"), '=', DB::raw("branches.id"));
        }
        if ($table == 'lead_statuses') {
            $query->leftJoin('lead_statuses as parent', DB::raw("$table.parent_id"), '=', DB::raw("parent.id"));
            // $query->leftJoin('lead_statuses as parent', 'lead_statuses.parent_id', '=', 'parent.id')
            //     ->addSelect('parent.name as parent_name');
        }

        // Aliased select columns
        $columns = array_map(function ($col) use ($table) {
            if ($table === 'states' && $col === 'country_id') {
                return 'countries.name as country_name';
            }
            if ($table === 'cities') {
                if ($col === 'country_id') {
                    return 'countries.name as country_name';
                }
                if ($col === 'state_id') {
                    return 'states.name as state_name';
                }
            }
            if ($table === 'campaigns' && $col === 'campaign_type_id') {
                return 'campaign_types.name as campaign_type_name';
            }
            if ($table === 'faq_sub_categories' && $col === 'faq_category_id') {
                return 'faq_categories.name as faq_category_name';
            }
            if ($table === 'faqs') {
                if ($col === 'faq_category_id') {
                    return 'faq_categories.name as faq_category_name';
                }
                if ($col === 'faq_sub_category_id') {
                    return 'faq_sub_categories.name as faq_sub_category_name';
                }
            }
            if ($table === 'users')
            {
                if($col === 'role_id') {
                    return 'roles.role_name as role_name';
                }
                if($col === 'branch_id'){
                    return 'branches.name as branch_name';
                }
            }

            if ($table === 'lead_statuses' && $col === 'parent_id') {
                return 'parent.name as parent_name';
            }
            return str_contains($col, '.') ? $col : "$table.$col";
        }, $rawColumns);

        $query->select($columns);

        // Apply conditions
        $conditions = json_decode(urldecode($request->input('conditions', '[]')), true);
        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                if (isset($condition['column'], $condition['operator'], $condition['value'])) {
                    $column = str_contains($condition['column'], '.') ? $condition['column'] : "$table.{$condition['column']}";
                    $query->where($column, $condition['operator'], $condition['value']);
                }
            }
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $baseCol = explode(' as ', $col)[0];
                    $q->orWhere($baseCol, 'ILIKE', "%{$search}%");
                }
            });
        }

        // Count before pagination
        $total = (clone $query)->count();

        // Paginate
        $data = $query->orderBy("$table.$orderBy", $orderType)
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->encoded_id = urlencode(base64_encode($item->id));
                return $item;
            });

        return response()->json([
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit),
        ]);
    }

    public function export(Request $request)
    {
        $table = $request->input('table');
        $columns = explode(',', $request->input('columns'));
        $titles = explode(',', $request->input('headers', '')); // <-- NEW
        $format = $request->input('format', 'csv');
        $search = $request->input('search');

        $filename = $request->input('filename'); // Optional
        $defaultName = $table . '_export_' . now()->format('Y-m-d_H-i-s');
        $filename = $filename ?: $defaultName;

        $columns = array_filter($columns, fn($col) => strtolower($col) !== 'actions');

        $query = DB::table($table)->select($columns);

        $conditions = json_decode(urldecode($request->input('conditions')), true);
        if (!empty($conditions)) {
            foreach ($conditions as $condition) {
                if (isset($condition['column'], $condition['operator'], $condition['value'])) {
                    $query->where($condition['column'], $condition['operator'], $condition['value']);
                }
            }
        }

        if ($search) {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', '%' . $search . '%');
                }
            });
        }

        $rawData = $query->get()->toArray();

        // Add Sl. No. to data
        $data = [];
        foreach ($rawData as $index => $row) {
            $data[] = array_merge(['Sl. No.' => $index + 1], (array) $row);
        }

        // Add Sl. No. to headings
        $columns = array_merge(['Sl. No.'], $columns);

        // Fallback to raw column names if no custom titles given
        $headers = count($titles) === count($columns) ? $titles : $columns;

        $titles[] = 'Status';
        
        switch ($format) {
            case 'csv':
                return $this->exportCsv($titles, $data, $filename . '.csv');

            case 'excel':
                return Excel::download(new \App\Exports\ArrayExport($columns, $data), 'export.xlsx');

            case 'pdf':
                $pdf = PDF::loadView('exports.table', ['columns' => $headers, 'headers' => $titles, 'data' => $data]);
                return $pdf->download($filename . '.pdf');
        }

        return response()->json(['error' => 'Invalid format'], 400);
    }

    protected function exportCsv($columns, $data, $filename)
    {
        // $filename = 'export.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=$filename");

        // Write headers
        fputcsv($handle, $columns);

        // Write each row
        foreach ($data as $row) {
            fputcsv($handle, array_values($row));
        }

        fclose($handle);
        exit;
    }
}