<?php

namespace Wcms;

class Route extends Item
{
    protected $id = null;
    protected $aff = null;
    protected $action = null;
    protected $redirect = null;

    public const AFF = ['read', 'edit', 'admin', 'media'];

    public function __construct($vars)
    {
        $this->hydrate($vars);
    }

    public function toarray()
    {
        $array = [];
        if (!empty($this->id)) {
            $array[] = 'page';
        }
        if (!empty($this->aff)) {
            $array[] = 'aff=' . $this->aff;
        }
        if (!empty($this->action)) {
            $array[] = 'action=' . $this->action;
        }
        if (!empty($this->redirect)) {
            $array[] = $this->redirect;
        }


        return $array;
    }

    public function tostring()
    {
        return implode(' ', $this->toarray());
    }



    public function setid($id)
    {
        $this->id = $id;
    }

    public function setaff($aff)
    {
        $this->aff = $aff;
    }

    public function setaction($action)
    {
        $this->action = $action;
    }

    public function setredirect($redirect)
    {
        $this->redirect = $redirect;
    }

    public function id()
    {
        return $this->id;
    }
}
