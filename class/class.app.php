<h4>class.app</h4>
<?php
class App
{
	private $bdd;

	public function __construct($config)
	{
		try {
			$this->bdd = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8', $config['user'], $config['password']);
		} catch (Exeption $e) {
			die('Erreur : ' . $e->getMessage());
		}

		// try {
		// 	$this->bdd = new PDO('mysql:host=localhost;dbname=wcms;charset=utf8', 'root', '');
		// } catch (Exeption $e) {
		// 	die('Erreur : ' . $e->getMessage());
		// }
	}

	public function add(Art $art)
	{

		if ($this->exist($art->id())) {
			echo '<p>cet id existe deja</p>';
		} else {

			$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

			$q = $this->bdd->prepare('INSERT INTO art(id, titre, soustitre, intro, tag, datecreation, datemodif, css, html, secure, couleurtext, couleurbkg, couleurlien) VALUES(:id, :titre, :soustitre, :intro, :tag, :datecreation, :datemodif, :css, :html, :secure, :couleurtext, :couleurbkg, :couleurlien)');

			$q->bindValue(':id', $art->id());
			$q->bindValue(':titre', $art->titre());
			$q->bindValue(':soustitre', $art->soustitre());
			$q->bindValue(':intro', $art->intro());
			$q->bindValue(':tag', $art->tag());
			$q->bindValue(':datecreation', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
			$q->bindValue(':css', $art->css());
			$q->bindValue(':html', $art->html());
			$q->bindValue(':secure', $art->secure());
			$q->bindValue(':couleurtext', $art->couleurtext());
			$q->bindValue(':couleurbkg', $art->couleurbkg());
			$q->bindValue(':couleurlien', $art->couleurlien());

			$q->execute();
		}
	}

	public function delete(Art $art)
	{
		$req = $this->bdd->prepare('DELETE FROM art WHERE id = :id ');
		$req->execute(array('id' => $art->id()));
		$req->closeCursor();
	}

	public function get($id)
	{
		$req = $this->bdd->prepare('SELECT * FROM art WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return new Art($donnees);

		$req->closeCursor();

	}

	public function getlist()
	{
		$list = [];

		$req = $this->bdd->query('SELECT * FROM art ORDER BY id');
		while ($donnees = $req->fetch(PDO::FETCH_ASSOC))
		{
		  $list[] = new Art($donnees);
		}
		return $list;
	}

	public function count()
	{
		return $this->bdd->query('SELECT COUNT(*) FROM art')->fetchColumn();
	}

	public function exist($id)
	{
		$req = $this->bdd->prepare('SELECT COUNT(*) FROM art WHERE id = :id ');
		$req->execute(array('id' => $id));
		$donnees = $req->fetch(PDO::FETCH_ASSOC);

		return (bool)$donnees['COUNT(*)'];
	}

	public function update(Art $art)
	{
		$now = new DateTimeImmutable(null, timezone_open("Europe/Paris"));

		$q = $this->bdd->prepare('UPDATE art SET titre = :titre, soustitre = :soustitre, intro = :intro, tag = :tag, datecreation = :datecreation, datemodif = :datemodif, css = :css, html = :html, secure = :secure, couleurtext = :couleurtext, couleurbkg = :couleurbkg, couleurlien = :couleurlien WHERE id = :id');

		$q->bindValue(':id', $art->id());
		$q->bindValue(':titre', $art->titre());
		$q->bindValue(':soustitre', $art->soustitre());
		$q->bindValue(':intro', $art->intro());
		$q->bindValue(':tag', $art->tag());
		$q->bindValue(':datecreation', $art->datecreation()->format('Y-m-d H:i:s'));
		$q->bindValue(':datemodif', $now->format('Y-m-d H:i:s'));
		$q->bindValue(':css', $art->css());
		$q->bindValue(':html', $art->html());
		$q->bindValue(':secure', $art->secure(), PDO::PARAM_INT);
		$q->bindValue(':couleurtext', $art->couleurtext());
		$q->bindValue(':couleurbkg', $art->couleurbkg());
		$q->bindValue(':couleurlien', $art->couleurlien());

		$q->execute();
	}


}
?>