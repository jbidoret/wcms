<?php
class Modelart extends Modeldb
{

	const SELECT = ['title', 'id', 'description', 'tag', 'date', 'datecreation', 'datemodif', 'daterender', 'css', 'quickcss', 'javascript', 'body', 'header', 'main', 'nav', 'aside', 'footer', 'render', 'secure', 'invitepassword', 'interface', 'linkfrom', 'linkto', 'template', 'affcount', 'editcount'];
	const BY = ['datecreation', 'title', 'id', 'description', 'datemodif', 'secure'];
	const ORDER = ['DESC', 'ASC'];


	public function __construct()
	{
		parent::__construct();
	}

	public function add(Art2 $art)
	{

		$artdata = new \JamesMoss\Flywheel\Document($art->dry());
		$artdata->setId($art->id());
		$this->artstore->store($artdata);
	}


	public function get($id)
	{
		if ($id instanceof Art2) {
			$id = $id->id();
		}
		if (is_string($id)) {
			$artdata = $this->artstore->findById($id);
			if ($artdata !== false) {
				return new Art2($artdata);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getartelement($id, $element)
	{
		if (in_array($element, Model::TEXT_ELEMENTS)) {
			$art = $this->get($id);
			if ($art !== false) {
				return $art->$element();
			} else {
				return '';
			}
		}
	}

	public function delete(Art2 $art)
	{
		$this->artstore->delete($art->id());
		$this->unlink($art->id());
	}


	public function unlink(string $artid)
	{
		$files = ['.css', '.quick.css', '.js'];
		foreach ($files as $file) {
			if (file_exists(Model::RENDER_DIR . $artid . $file)) {
				unlink(Model::RENDER_DIR . $artid . $file);
			}
		}
	}

	public function update(Art2 $art)
	{
		$artdata = new \JamesMoss\Flywheel\Document($art->dry());
		$artdata->setId($art->id());
		$this->artstore->store($artdata);
	}

	public function artcompare($art1, $art2, $method = 'id', $order = 1)
	{
		$result = ($art1->$method('sort') <=> $art2->$method('sort'));
		return $result * $order;
	}

	public function buildsorter($sortby, $order)
	{
		return function ($art1, $art2) use ($sortby, $order) {
			$result = $this->artcompare($art1, $art2, $sortby, $order);
			return $result;
		};
	}



	public function artlistsort(&$artlist, $sortby, $order = 1)
	{
		return usort($artlist, $this->buildsorter($sortby, $order));
	}


	/**
	 * @param array $artlist List of Art2
	 * @param array $tagchecked list of tags
	 * @param string $tagcompare string, can be 'OR' or 'AND', set the tag filter method
	 * @return array $array
	 */

	public function filtertagfilter(array $artlist, array $tagchecked, $tagcompare = 'OR')
	{

		$filteredlist = [];
		foreach ($artlist as $art) {
			if (empty($tagchecked)) {
				$filteredlist[] = $art->id();
			} else {
				$inter = (array_intersect($art->tag('array'), $tagchecked));
				if ($tagcompare == 'OR') {
					if (!empty($inter)) {
						$filteredlist[] = $art->id();
					}
				} elseif ($tagcompare == 'AND') {
					if (!array_diff($tagchecked, $art->tag('array'))) {
						$filteredlist[] = $art->id();
					}
				}
			}
		}
		return $filteredlist;
	}

	public function filtersecure(array $artlist, $secure)
	{
		$filteredlist = [];
		foreach ($artlist as $art) {
			if ($art->secure() == intval($secure)) {
				$filteredlist[] = $art->id();
			} elseif (intval($secure) >= 4) {
				$filteredlist[] = $art->id();
			}
		}
		return $filteredlist;
	}


	public function tag(array $artlist, $tagchecked)
	{
		$artcheckedlist = [];
		foreach ($artlist as $art) {
			if (in_array($tagchecked, $art->tag('array'))) {
				$artcheckedlist[] = $art;
			}
		}
		return $artcheckedlist;
	}

	public function taglist(array $artlist, array $tagcheckedlist)
	{
		$taglist = [];
		foreach ($tagcheckedlist as $tag) {
			$taglist[$tag] = $this->tag($artlist, $tag);
		}
		return $taglist;
	}

	/**
	 * @param array $taglist list of tags
	 * @param array $artlist list of Art2
	 * @return array list of tags each containing list of id
	 */

	public function tagartlist(array $taglist, array $artlist)
	{
		$tagartlist = [];
		foreach ($taglist as $tag) {
			$tagartlist[$tag] = $this->filtertagfilter($artlist, [$tag]);
		}
		return $tagartlist;
	}

	public function lasteditedartlist(int $last, array $artlist)
	{
		$this->artlistsort($artlist, 'datemodif', -1);
		$artlist = array_slice($artlist, 0, $last);
		$idlist = [];
		foreach ($artlist as $art) {
			$idlist[] = $art->id();
		}
		return $idlist;
	}

}