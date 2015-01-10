<?php
namespace ABCMath\Db;

use ABCMath\Base;

class Datatable extends Base
{
    public $sql;
    public $columns;

    public function __construct()
    {
        parent::__construct();
        $this->sql = '';
        $this->columns = array();
    }

    private function _buildLimit()
    {
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT ".intval($_GET['iDisplayStart']).", ".
                intval($_GET['iDisplayLength']);
        }

        return $sLimit;
    }

    private function _buildOrder()
    {
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i<intval($_GET['iSortingCols']); $i++) {
                if ($_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true") {
                    $sOrder .= "`".$this->columns[ intval($_GET['iSortCol_'.$i]) ]."` ".
                        ($_GET['sSortDir_'.$i] === 'asc' ? 'asc' : 'desc').", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        return $sOrder;
    }

    private function _buildWhere()
    {
        $sWhere = "";
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $sWhere = "WHERE (";
            for ($i = 0; $i<count($this->columns); $i++) {
                $sWhere .= "`".$this->columns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        return $sWhere;
    }

    public function processQuery()
    {
        $sLimit = $this->_buildLimit();
        $sOrder = $this->_buildOrder();
        $sWhere = $this->_buildWhere();

        /* Individual column filtering */
        for ($i = 0; $i<count($this->columns); $i++) {
            if (isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '') {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= "`".$this->columns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
            }
        }

        /*
         * SQL queries
         * Get data to display
         */
        $sQuery = "
			SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $this->columns))."`
			FROM   ({$this->sql}) t
			$sWhere
			$sOrder
			$sLimit
			";

        $stmt = $this->_conn->prepare($sQuery);
        $stmt->execute();
        $rResult = $stmt->fetchAll();

        /* Data set length after filtering */
        $sQuery = "
			SELECT FOUND_ROWS() f
		";

        $stmt = $this->_conn->prepare($sQuery);
        $stmt->execute();
        $iFilteredTotal = $stmt->fetch();

        /* Total data set length */
        $sQuery = "
			SELECT COUNT(*) c
			FROM   ({$this->sql}) t
		";
        $stmt = $this->_conn->prepare($sQuery);
        $stmt->execute();
        $iTotal = $stmt->fetch();

        /*
         * Output
         */
        $output = array(
            "sEcho" => intval(isset($_GET['sEcho']) ? $_GET['sEcho'] : 0),
            "iTotalRecords" => $iTotal['c'],
            "iTotalDisplayRecords" => $iFilteredTotal['f'],
            "aaData" => array(),
        );

        foreach ($rResult as $aRow) {
            $row = array();
            for ($i = 0; $i<count($this->columns); $i++) {
                if ($this->columns[$i] == "version") {
                    /* Special output formatting for 'version' column */
                    $row[] = ($aRow[ $this->columns[$i] ] == "0") ? '-' : $aRow[ $this->columns[$i] ];
                } elseif ($this->columns[$i] != ' ') {
                    /* General output */
                    $row[] = $aRow[ $this->columns[$i] ];
                }
            }
            $output['aaData'][] = $row;
        }

        //echo json_encode( $output );

        return $output;
    }
}
