<?php
class App
{
	private $bdd;
	private $session;
	private $arttable;


	const CONFIG_FILE = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'w.config.json';
	const GLOBAL_CSS_DIR = '.' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'global' . DIRECTORY_SEPARATOR . 'global.css';
	const MEDIA_DIR = '.' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR;
	const MEDIA_EXTENSIONS = array('jpeg', 'jpg', 'JPG', 'png', 'gif', 'mp3', 'mp4', 'mov', 'wav', 'flac', 'pdf');
	const MEDIA_TYPES = ['image', 'video', 'sound', 'other'];


	const ADMIN = 10;
	const EDITOR = 3;
	const INVITE = 2;
	const READ = 1;
	const FREE = 0;


// _____________________________________ C O N S T R U C T _________________________________



	public function __construct()
	{
		$this->setsession($this::FREE);
	}

	public function setbdd(Config $config)
	{
		$caught = true;

		try {
			$this->bdd = new PDO('mysql:host=' . $config->host() . ';dbname=' . $config->dbname() . ';charset=utf8', $config->user(), $config->password(), array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT));
			//$this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$caught = false;
			echo '<h1>Error 500, database offline</h1>';
			if ($this->session() >= self::EDITOR) {
				echo '<p>Error : ' . $e->getMessage() . '</p>';
				if ($this->session() == self::ADMIN) {
					echo '<p>Go to the <a href="?aff=admin">Admin Panel</a> to edit your database credentials</p>';
				} else {
					echo '<p>Logout and and come back with an <strong>admin password</strong> to edit the database connexions settings.</p>';
				}
			} else {
				echo '<p><a href=".">Homepage for admin login</a> (connect on the top right side)</p>';
			}
			exit;
		}

		return $caught;

	}

	public function settable(Config $config)
	{
		if (!empty($config->arttable())) {
			$this->arttable = $config->arttable();
		} else {
			echo '<h1>Table Error</h1>';

			if ($this->session() >= self::EDITOR) {
				if ($this->session() == self::ADMIN) {
					echo '<p>Go to the <a href="?aff=admin">Admin Panel</a> to select or add an Article table</p>';
				} else {
					echo '<p>Logout and and come back with an <strong>admin password</strong> to edit table settings.</p>';
				}
			} else {
				echo '<p><a href=".">Homepage for admin login</a> (connect on the top right side)</p>';
			}
			$caught = false;
			exit;
		}
	}

	public function bddinit(Config $config)
	{
		$test = $this->setbdd($config);
		if ($test) {
			$this->settable($config);
		}
	}


// _________________________________________ C O N F I G ____________________________________

	public function readconfig()
	{
		if (file_exists(self::CONFIG_FILE)) {
			$current = file_get_contents(self::CONFIG_FILE);
			$donnees = json_decode($current, true);
			return new Config($donnees);
		} else {
			return 0;
		}

	}

	public function createconfig(array $donnees)
	{
		return new Config($donnees);
	}


	public function savejson(string $json)
	{
		file_put_contents(self::CONFIG_FILE, $json);
	}






// ___________________________________________ A R T ____________________________________


	public function add(Art2 $art)
	{

		if ($this->exist($art->id())) {
			echo '<span class="alert">idalreadyexist</span>';
		} else {

			var_dump($art);

			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

			$request = 'INSERT INTO ' . $this->arttable . '(id, title, description, tag, date, datecreation, datemodif, daterender, css, quickcss, javascript, html, header, section, nav, aside, footer, render, secure, invitepassword, interface, linkfrom, template, affcount, editcount)
						VALUES(:id, :title, :description, :tag, :date, :datecreation, :datemodif, :daterender, :css, :quickcss, :javascript, :html, :header, :section, :nav, :aside, :footer, :render, :secure, :invitepassword, :interface, :linkfrom, :template, :affcount, :editcount)';

			$q = $this->bdd->prepare($request);

			$q->bindValue(':id', $art->id());
			$q->bindValue(':title', $art->title());
			$q->bindValue(':description', $art->description());
			$q->bindValue(':tag', $art->tag('string'));
			$q->bindValue(':date', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':datecreation', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':daterender', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':css', $art->css());
			$q->bindValue(':quickcss', $art->quickcss('json'));
			$q->bindValue(':javascript', $art->javascript());
			$q->bindValue(':html', $art->html());
			$q->bindValue(':header', $art->header());
			$q->bindValue(':section', $art->md());
			$q->bindValue(':nav', $art->nav());
			$q->bindValue(':aside', $art->aside());
			$q->bindValue(':footer', $art->footer());
			$q->bindValue(':render', $art->render());
			$q->bindValue(':secure', $art->secure());
			$q->bindValue(':invitepassword', $art->invitepassword());
			$q->bindValue(':interface', $art->interface());
			$q->bindValue(':linkfrom', $art->linkfrom('json'));
			$q->bindValue(':template', $art->template('json'));
			$q->bindValue(':affcount', $art->affcount());
			$q->bindValue(':editcount', $art->editcount());

			$q->execute();
		}
	}

	public function delete(Art2 $art)
	{
		$req = $this->bdd->prepare('DELETE FROM ' . $this->arttable . ' WHERE id = :id ');
		$req->execute(array('id' => $art->id()));
		$req->closeCursor();
	}

	public function get($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM ' . $this->arttable . ' WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return new Art2($donnees);

		$req->closeCursor();

	}





	public function update(Art2 $art)
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

		//$request = 'UPDATE ' . $this->arttable . '(id, title, description, tag, date, datecreation, datemodif, daterender, css, quickcss, javascript, html, header, section, nav, aside, footer, render, secure, invitepassword, interface, linkfrom, template, affcount, editcount)	VALUES(:id, :title, :description, :tag, :date, :datecreation, :datemodif, :daterender, :css, :quickcss, :javascript, :html, :header, :section, :nav, :aside, :footer, :render, :secure, :invitepassword, :interface, :linkfrom, :template, :affcount, :editcount) WHERE id = :id';

		$request = 'UPDATE ' . $this->arttable . ' SET id = :id, title = :title, description = :description, tag = :tag, date = :date, datecreation = :datecreation, datemodif = :datemodif, daterender = :daterender, css = :css, quickcss = :quickcss, javascript = :javascript, html = :html, header = :header, section = :section, nav = :nav, aside = :aside, footer = :footer, render = :footer, secure = :secure, invitepassword = :invitepassword, interface = :interface, linkfrom = :linkfrom, template = :template, affcount = :affcount, editcount = :editcount WHERE id = :id';

		$q = $this->bdd->prepare($request);

		$q->bindValue(':id', $art->id());
		$q->bindValue(':title', $art->title());
		$q->bindValue(':description', $art->description());
		$q->bindValue(':tag', $art->tag('string'));
		$q->bindValue(':date', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':datecreation', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':daterender', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':css', $art->css());
		$q->bindValue(':quickcss', $art->quickcss('json'));
		$q->bindValue(':javascript', $art->javascript());
		$q->bindValue(':html', $art->html());
		$q->bindValue(':header', $art->header());
		$q->bindValue(':section', $art->md());
		$q->bindValue(':nav', $art->nav());
		$q->bindValue(':aside', $art->aside());
		$q->bindValue(':footer', $art->footer());
		$q->bindValue(':render', $art->render());
		$q->bindValue(':secure', $art->secure());
		$q->bindValue(':invitepassword', $art->invitepassword());
		$q->bindValue(':interface', $art->interface());
		$q->bindValue(':linkfrom', $art->linkfrom('json'));
		$q->bindValue(':template', $art->template('json'));
		$q->bindValue(':affcount', $art->affcount());
		$q->bindValue(':editcount', $art->editcount());

		$q->execute();
	}

	public function exist($id)
	{
		$req = $this->bdd->prepare(' SELECT COUNT(*) FROM ' . $this->arttable . ' WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return (bool)$donnees['COUNT(*)'];
	}





	//____________________________________________ L S T ______________________________



	public function getlister(array $selection = ['id'], array $opt = [])
	{
		// give an array using SELECTION columns and sort and desc OPTIONS 

		$default = ['tri' => 'id', 'desc' => 'DESC'];
		$opt = array_update($default, $opt);

		$list = [];
		$option = ['datecreation', 'title', 'id', 'description', 'datemodif', 'tag', 'secure'];
		if (is_array($selection) && is_string($opt['tri']) && strlen($opt['tri']) < 16 && is_string($opt['desc']) && strlen($opt['desc']) < 5 && in_array($opt['tri'], $option)) {

			$selection = implode(", ", $selection);

			$select = 'SELECT ' . $selection . ' FROM ' . $this->arttable . ' ORDER BY ' . $opt['tri'] . ' ' . $opt['desc'];
			$req = $this->bdd->query($select);
			while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
				$list[] = new Art2($donnees);
			}
			return $list;
		}
	}






	public function getlisteropt(Opt $opt)
	{

		$artlist = [];

		$select = 'SELECT ' . $opt->col('string') . ' FROM ' . $this->arttable;
		$req = $this->bdd->query($select);
		while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
			$artlist[] = new Art2($donnees);
		}
		return $artlist;

	}

	public function listcalclinkfrom(&$artlist)
	{
		foreach ($artlist as $art) {
			$art->calclinkto($artlist);
		}
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


	public function lister()
	{
		$req = $this->bdd->query(' SELECT * FROM ' . $this->arttable . ' ORDER BY id ');
		$donnees = $req->fetchAll(PDO::FETCH_ASSOC);
		$req->closeCursor();
		return $donnees;

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

	public function count()
	{
		return $this->bdd->query(' SELECT COUNT(*) FROM ' . $this->arttable . ' ')->fetchColumn();
	}


	
	// __________________________________________ T A B L E ________________________________________________________


	public function tableexist($dbname, $tablename)
	{

		$req = $this->bdd->prepare('SELECT COUNT(*)
		FROM information_schema.tables
		WHERE table_schema = :dbname AND
			  table_name like :tablename');
		$req->execute(array(
			'dbname' => $dbname,
			'tablename' => $tablename
		));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);
		$req->closeCursor();
		$exist = intval($donnees['COUNT(*)']);
		return $exist;




	}

	public function tablelist($dbname)
	{
		$request = 'SHOW TABLES IN ' . $dbname;
		$req = $this->bdd->query($request);
		$donnees = $req->fetchAll(PDO::FETCH_ASSOC);
		$req->closeCursor();

		$arttables = [];
		foreach ($donnees as $table) {
			$arttables[] = $table['Tables_in_' . $dbname];
		}
		return $arttables;


	}





	public function tableduplicate($dbname, $arttable, $tablename)
	{
		$arttable = strip_tags($arttable);
		$tablename = str_clean($tablename);
		if ($this->tableexist($dbname, $arttable) && !$this->tableexist($dbname, $tablename)) {
			$duplicate = " CREATE TABLE `$tablename` LIKE `$arttable`;";
			$alter = "ALTER TABLE `$tablename` ADD PRIMARY KEY (`id`);";
			$insert = "INSERT `$tablename` SELECT * FROM `$arttable`;";


			$req = $this->bdd->query($duplicate . $alter . $insert);

			return 'tableduplicated';
		} else {
			return 'tablealreadyexist';
		}
	}




// __________________________________________ M E D ________________________________________________________

	public function addmedia(array $file, $maxsize = 2 ** 24, $id)
	{
		$message = 'runing';
		$id = strtolower(strip_tags($id));
		$id = str_replace(' ', '_', $id);
		if (isset($file) and $file['media']['error'] == 0 and $file['media']['size'] < $maxsize) {
			$infosfichier = pathinfo($file['media']['name']);
			$extension_upload = $infosfichier['extension'];
			$extensions_autorisees = $this::MEDIA_EXTENSIONS;
			if (in_array($extension_upload, $extensions_autorisees)) {
				if (!file_exists($this::MEDIA_DIR . $id . '.' . $extension_upload)) {

					$extension_upload = strtolower($extension_upload);
					$uploadok = move_uploaded_file($file['media']['tmp_name'], $this::MEDIA_DIR . $id . '.' . $extension_upload);
					if ($uploadok) {
						$message = 'uploadok';
					} else {
						$message = 'uploaderror';
					}
				} else {
					$message = 'filealreadyexist';

				}
			}
		} else {
			$message = 'filetoobig';

		}

		return $message;
	}


	public function getmedia($entry, $dir)
	{
		$fileinfo = pathinfo($entry);

		$filepath = $fileinfo['dirname'] . '.' . $fileinfo['extension'];

		$donnees = array(
			'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
			'path' => $dir,
			'extension' => $fileinfo['extension']
		);



		return new Media($donnees);

	}

	public function getlistermedia($dir, $type = "all")
	{
		if ($handle = opendir($dir)) {
			$list = [];
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {

					$media = $this->getmedia($entry, $dir);


					$media->analyse();

					if (in_array($type, self::MEDIA_TYPES)) {
						if ($media->type() == $type) {
							$list[] = $media;
						}
					} else {
						$list[] = $media;
					}


				}
			}
			return $list;
		}

		return $list;

	}




	//_________________________________________________________ R E C ________________________________________________________


	public function getlisterrecord($dir)
	{
		if ($handle = opendir($dir)) {
			$list = [];
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$fileinfo = pathinfo($entry);

					$filepath = $dir . $fileinfo['filename'] . '.' . $fileinfo['extension'];

					list($width, $height, $type, $attr) = getimagesize($filepath);
					$filesize = filesize($filepath);

					$donnees = array(
						'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
						'path' => $fileinfo['dirname'],
						'extension' => $fileinfo['extension'],
						'size' => $filesize
					);

					$list[] = new Record($donnees);

				}
			}
		}

		return $list;



	}




	// ________________________________________________________ M A P ________________________________________________________


	public function map(array $getlister, $lb = PHP_EOL)
	{

		$map = "";
		$link = "";
		$style = "";
		foreach ($getlister as $item) {
			if($item->secure() == 2) {
				$style = $style . $lb . $item->id() . '{' . $item->title() . '}';
			} elseif ($item->secure() == 1) {
				$style = $style . $lb . $item->id() . '(' . $item->title() . ')';
				
			} else {
				$style = $style . $lb . $item->id() . '((' . $item->title() . '))';
			}
			foreach ($item->linkfrom('array') as $linkfrom) {
				$map = $map . $lb . $item->id() . ' --> ' . $linkfrom;
				$link = $link . $lb . 'click ' . $linkfrom . ' "./?id=' . $linkfrom . '"';
				
			}
			$link = $link . $lb . 'click ' . $item->id() . ' "./?id=' . $item->id() . '"';
		}
		return $map . $link . $style;

	}





	//_________________________________________________________ S E S ________________________________________________________

	public function login($pass, $config)
	{
		if (strip_tags($pass) == $config->admin()) {
			return $level = self::ADMIN;
		} elseif (strip_tags($pass) == $config->read()) {
			return $level = self::READ;
		} elseif (strip_tags($pass) == $config->editor()) {
			return $level = self::EDITOR;
		} elseif (strip_tags($pass) == $config->invite()) {
			return $level = self::INVITE;
		}
	}

	public function logout()
	{
		return $level = 0;
	}

	// ________________________________________________________ S E T ___________________________________________________


	public function setsession($session)
	{
		$this->session = $session;
	}



	
	//_________________________________________________________ G E T ________________________________________________________

	public function session()
	{
		return $this->session;
	}


}
?>