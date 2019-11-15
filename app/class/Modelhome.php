<?php

namespace Wcms;

class Modelhome extends Modelpage
{
 	
	public function __construct() {
		parent::__construct();
	}

    public function optinit($table)
    {

        $opt = new Opt(Page::classvarlist());
        $opt->setcol(['id', 'tag', 'linkfrom', 'linkto', 'description', 'title', 'datemodif', 'datecreation', 'date', 'secure', 'visitcount', 'editcount', 'affcount']);
        $opt->settaglist($table);
        $opt->setauthorlist($table);
        $opt->submit();

        return $opt;
    }

    /**
     * Initialise Optlist object using
     * 
     * @param array $table the list of all pages objects
     * 
     * @return Optlist Object initialized
     */
    public function Optlistinit(array $table)
    {
        $optlist = new Optlist(Page::classvarlist());
        $optlist->settaglist($table);
        $optlist->setauthorlist($table);

        return $optlist;
    }




    /**
     * @param array $table
     * @param Opt $opt
     */
    public function table2($table, $opt)
    {


        $filtertagfilter = $this->filtertagfilter($table, $opt->tagfilter(), $opt->tagcompare());
        $filterauthorfilter = $this->filterauthorfilter($table, $opt->authorfilter(), $opt->authorcompare());
        $filtersecure = $this->filtersecure($table, $opt->secure());

        $filter = array_intersect($filtertagfilter, $filtersecure, $filterauthorfilter);
        $table2 = [];
        $table2invert = [];
        foreach ($table as $page) {
            if (in_array($page->id(), $filter)) {
                $table2[] = $page;
            } else {
                $table2invert[] = $page;
            }


        }

        if (!empty($opt->invert())) {
            $table2 = $table2invert;
        }

        $this->pagelistsort($table2, $opt->sortby(), $opt->order());

        if($opt->limit() !== 0) {
            $table2 = array_slice($table2, 0, $opt->limit());
        }


        return $table2;
    }

    /**
     * @param array array of the columns to show from the user
     * 
     * @return array assoc each key columns to a boolean value to show or not
     */
    public function setcolumns(array $columns) : array
    {
        foreach (Model::COLUMNS as $col) {
            if(in_array($col, $columns)) {
                $showcols[$col] = true;
            } else {
                $showcols[$col] = false;
            }
        }
        return $showcols;
    }
}








?>