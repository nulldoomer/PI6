<?php

namespace App\DataTables;

use App\User;
use Laravolt\Avatar\Facade as Avatar;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DoctorsDatatable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('image', function ($query) {
                if (isset($query->image)) {
                    return '<img src="' . asset(path_user_image() . $query->image) . '" height="70" width="90" alt="' . __('No Image') . '"/>';
                } else {
                    return '<img src="' . Avatar::create($query->name)->toBase64() . '" height="70" width="90" alt="' . __('No Image') . '"/>';
                }
            })
            ->addColumn('category', function ($query) {
                return $query->doctor->category->name;
            })
            ->editColumn('created_at', function ($request) {
                return $request->created_at->diffForHumans(); // human readable format
            })
            ->addColumn(
                'action',
                function ($data) {
                    $btn = '';
                    $btn = $btn . '<a type="button" data-toggle="modal" data-target="#exampleModalCenter' . $data->id . '"><i class="fas fa-eye"></i></a>';
                    if (auth()->user()->can('doctor-edit')) {
                        $btn = $btn . ' <a href="' . route("doctor.show", $data->id) . '"><i class="ik ik-edit"></i></a>';
                    }
                    if (auth()->user()->can('doctor-delete')) {
                        $btn = $btn . '<a class="delete" href="' . route("user.delete", $data->id) . '"><i class="ik ik-trash-2"></i></a> ';
                    }
                    return $btn;
                }
            )
            ->rawColumns(['image', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\DoctorsDatatable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()->where('role', 'doctor')->with('doctor');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('users-table')->addTableClass('table-striped')->autoWidth()
            ->addIndex()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->serverSide(true)
            ->dom('lBfr<"table-responsive"t>ip')
            ->orderBy(0) //1 for ascending and 0 for descending
            ->language([
                'processing' => '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500 dt-table-processing"></i>'
            ])
            ->buttons(
                Button::make('colvis')->text('Mostrar')->addClass('btn-info')->text('<i class="fas fa-eye"></i> ' . __('Show/hide')),
                Button::make('copy')->addClass('btn-success hide-mobile')->text('<i class="far fa-copy"></i>' . __('Copy')),
                Button::make('export')->addClass('btn-warning'),
                Button::make('reset')->addClass('btn-info hide-mobile'),
                Button::make('reload')->addClass('btn-default hide-mobile')->text('<i class="fas fa-fw fa-sync"></i> ' . __('Reload')),
                Button::make('print')->addClass('btn-primary'),
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('No')->orderable(false)->searchable(false),
            //Column::make('id'),
            Column::make('image')->orderable(false),
            Column::make('name'),
            Column::make('category')->orderable(true),
            Column::make('email'),
            Column::make('created_at')->title('Fecha creación'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(200)
                ->addClass('table-actions'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Doctors_' . date('YmdHis');
    }
}
