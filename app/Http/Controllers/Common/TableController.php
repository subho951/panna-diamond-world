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
        if ($table === 'companies') {
            $query->leftJoin('industries', DB::raw("$table.industry_id"), '=', DB::raw("industries.id"));
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
        }

        // Aliased select columns
        $columns = array_map(function ($col) use ($table) {
            if ($table === 'companies' && $col === 'industry_id') {
                return 'industries.name as industry_name';
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
            if ($table === 'users' && $col === 'role_id') {
                return 'roles.role_name as role_name';
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