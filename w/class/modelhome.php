<?php

class Modelhome extends Modeldb
{
 	
	public function __construct() {
		parent::__construct();
	}

    public function optinit($table)
    {

        $opt = new Opt(Art2::classvarlist());
        $opt->setcol(['id', 'tag', 'linkfrom', 'linkto', 'description', 'title', 'datemodif', 'datecreation', 'secure']);
        //$table = $listmanager->getlisteropt($opt);
        //$listmanager->listcalclinkfrom($table);
        $opt->settaglist($table);
        $opt->submit();



        return $opt;
    }

    public function optupdate()
    {
        
    }


    public function table2($table, $opt)
    {
        $listmanager = new Modelart;


        $filtertagfilter = $listmanager->filtertagfilter($table, $opt->tagfilter(), $opt->tagcompare());
        $filtersecure = $listmanager->filtersecure($table, $opt->secure());

        $filter = array_intersect($filtertagfilter, $filtersecure);
        $table2 = [];
        $table2invert = [];
        foreach ($table as $art) {
            if (in_array($art->id(), $filter)) {
                $table2[] = $art;
            } else {
                $table2invert[] = $art;
            }


        }

        if (!empty($opt->invert())) {
            $table2 = $table2invert;
        }

        $listmanager->artlistsort($table2, $opt->sortby(), $opt->order());


        return $table2;
    }
}








?>