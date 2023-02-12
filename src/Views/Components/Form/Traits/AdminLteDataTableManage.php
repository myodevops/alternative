<?php
namespace myodevops\ALTErnative\Views\Components\Form\Traits;

/*
Example:
http://127.0.0.1:8000/api/users?draw=1
&columns[0][data]=id&columns[0][name]=&columns[0][searchable]=false&columns[0][orderable]=true&columns[0][search][value]=&columns[0][search][regex]=false
&columns[1][data]=name&columns[1][name]=&columns[1][searchable]=true&columns[1][orderable]=true&columns[1][search][value]=&columns[1][search][regex]=false
&columns[2][data]=email&columns[2][name]=&columns[2][searchable]=true&columns[2][orderable]=true&columns[2][search][value]=&columns[2][search][regex]=false
&columns[3][data]=role&columns[3][name]=&columns[3][searchable]=true&columns[3][orderable]=false&columns[3][search][value]=&columns[3][search][regex]=false
&order[0][column]=1&order[0][dir]=asc&start=0&length=10&search[value]=&search[regex]=false&_=1670540022629
*/

trait AdminLteDataTableManage
{
    private $errorText = "";

    /**
     * Undocumented function
     *
     * @param Array $get
     * @param Object $model
     * @param String $fields
     * @return void
     */
    public function getData ($get, $model, $jsonRes) {
        // Check the configuration
        if (!$this->chechConfiguration ($get)) {
            return ($this->setError($this->errorText));
        }

        // Calculate the total of the records of the model
        $totalcount = $model->count ();
        $filteredcount = $totalcount;

        // Apply the filter
        $query = $model->query();
        if ($get['search']['value'] !== '') {
            $this->applyLikeFilter($query, $get);
            // Calculate the total of the records filtered
            $filteredcount = $query->count ();
        }

        // Apply the order
        $this->applyOrder($query, $get);

        // Apply the page start and the no. of viewing records
        $query = $query->skip($get['start'])
                       ->take($get['length']);
        $data = $query->get();

        // Add the column of the buttons
        $this->addButtons($data);

        // Create the data matrix with only the requested columns
        $data = $jsonRes::collection($data);

        $response = (object)array ("recordsTotal" => $totalcount, "recordsFiltered" => $filteredcount);
        $response->data = $data;

        return $response;
    }

    private function chechConfiguration ($get) {
        // Chach if the get variables are defined
        switch (true) {
            case !isset ($get['start']):
            case !isset ($get['length']):
            case !isset ($get['search']['value']):
            case !is_array ($get['columns']):
                $this->errorText = 'Bad definition of the get variables in AdminLteDataTableManage::getData';
                return false;
        }

        // Check if the columns configuration are propertly defined
        foreach ($get['columns'] as $column) {
            switch (true) {
                case !isset ($column['name']):
                    $this->errorText = 'Bad definition of the get columns configuration in AdminLteDataTableManage::getData';
                    return false;
            }
        }

        return true;
    }

    private function applyLikeFilter (&$query, $get) {
        // Apply the filters to the columns that are filterables
        $isfirst = true;
        foreach ($get['columns'] as $column) {
            // Determine if the column is filterable
            $filterable = true;
            if (isset ($column['searchable'])) {
                if ($column['searchable'] === 'false') {
                    $filterable = false;
                }
            }

            if ($filterable) {
                // Filter the columns based on filter string
                if ($isfirst) {
                    $query = $query->where($column['data'], 'LIKE', '%' . $get['search']['value'] . '%');
                } else {
                    $query = $query->orwhere($column['data'], 'LIKE', '%' . $get['search']['value'] . '%');
                }    
                $isfirst = false;
            }
        }
    }

    private function applyOrder (&$query, $get) {
        // Apply the order to the column that are ordable
        if (isset ($get['order'][0])) {
            $query->orderBy ($get['columns'][$get['order'][0]['column']]['data'], $get['order'][0]['dir']);
        }
    }

    private function addButtons (&$data) {
        // Add the buttons on every line
        foreach ($data as $line) {
            $line->actions = '<nobr>' . view('alternative::components.form.datatable-actions', ['id' => $line->id]) . '</nobr>';
        }
    }

    private function setError ($error) {
        return json_decode('{ "Error": ' . $error . ' }');
    }
}