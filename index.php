<?php
ini_set('display_errors', '1');
error_reporting(-1);
$conf = include( dirname(__FILE__)."/conf.php" );
include( dirname(dirname(__FILE__))."/Teinte/Web.php" );
include( dirname(dirname(__FILE__))."/Teinte/Base.php" );
$base = new Teinte_Base( $conf['sqlite'] );
$path = Teinte_Web::pathinfo(); // document demandé
$basehref = Teinte_Web::basehref(); //
$teinte = $basehref."../Teinte/";

// chercher le doc dans la base
$docid = current( explode( '/', $path ) );
$query = $base->pdo->prepare("SELECT * FROM doc WHERE code = ?; ");
$query->execute( array( $docid ) );
$doc = $query->fetch();

$q = null;
if ( isset($_REQUEST['q']) ) $q=$_REQUEST['q'];

?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title><?php
if( $doc ) echo $doc['title'].' — ';
echo $conf['title'];
    ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $teinte ?>tei2html.css" />
    <link rel="stylesheet" type="text/css" href="<?= $basehref ?>../theme/obvil.css"/>
  </head>
  <body>
    <div id="center">
      <header id="header">
        <h1><?php
if ( !$path && $base->search ) {
  echo '<a href="'.$basehref.'">'.$conf['title'].'</a>';
}
else if ( !$path ) {
  echo '<a href="//obvil.paris-sorbonne.fr/projets/danse">Obvil, projet : '.$conf['title'].'</a>';
}
else {
  echo '<a href="'.$basehref.'?'.$_COOKIE['lastsearch'].'">'.$conf['title'].'</a>';
}
        ?></h1>
        <a class="logo" href="http://obvil.paris-sorbonne.fr/"><img class="logo" src="<?php echo $basehref; ?>../theme/img/logo-obvil.png" alt="OBVIL"></a>
      </header>
      <div id="contenu">
        <aside id="aside">
          <?php
if ( $doc ) {
  echo '<nav id="download" title="Téléchargements">
  | <a target="_blank" href="http://obvil.github.io/danse/xml/'.$doc['code'].'.xml">xml</a>
  | <a target="_blank" href="'.$basehref.'epub/'.$doc['code'].'.epub">epub</a>
  | <a target="_blank" href="'.$basehref.'kindle/'.$doc['code'].'.mobi">kindle</a>
  | <a target="_blank" href="markdown/'.$doc['code'].'.md" title="Markdown">texte brut</a>
  | <a target="_blank" href="iramuteq/'.$doc['code'].'.txt">iramuteq</a>
  | <a target="_blank" href="html/'.$doc['code'].'.html">html</a>
';
  echo '<p> </p>';
  echo '
<header>
  <a class="title" href="'.$basehref.$doc['code'].'">'.$doc['title'].'</a>
</header>

<form action="#mark1">
  <a title="Retour aux résultats" href="'.$basehref.'?'.$_COOKIE['lastsearch'].'"><img src="'.$basehref.'../theme/img/fleche-retour-corpus.png" alt="←"/></a>
  <input name="q" value="'.str_replace( '"', '&quot;', $base->p['q'] ).'"/><button type="submit">🔎</button>
</form>
';
  // table des matières, quand il y en a une
  if ( file_exists( $f="toc/".$doc['code']."_toc.html" ) ) readfile( $f );
}
// accueil ? formulaire de recherche général
else {
  echo "\n".'<nav id="download" title="Téléchargements">
  | <a target="_blank" href="http://obvil.github.io/danse/xml/" title="Source XML/TEI">tei</a>
  | <a target="_blank" href="epub/" title="Livre électronique">epub</a>
  | <a target="_blank" href="kindle/" title="Mobi, format propriétaire Amazon">kindle</a>
  | <a target="_blank" href="markdown/" title="Markdown">texte brut</a>
  | <a target="_blank" href="iramuteq/">iramuteq</a>
  | <a target="_blank" href="html/">html</a>
  </nav>';
  echo '<p> </p>';
  echo'
<form action="">
  <input style="width: 100%;" name="q" class="text" placeholder="Rechercher de mots" value="'.str_replace( '"', '&quot;', $base->p['q'] ).'"/>
  <button type="submit" style="float: right; ">Rechercher</button>
</form>
  ';
}
          ?>
        </aside>
        <div id="main">
          <div id="article" class="<?php echo $doc['class']; ?>">
            <?php
if ( $doc ) {
  $html = file_get_contents( "article/".$doc['code']."_art.html" );
  if ( $q ) echo $base->hilite( $doc['id'], $q, $html );
  else echo $html;
}
else if ( $base->search ) {
  $base->biblio( array( "no", "date", "title", "occs" ), "SEARCH" );
}
// pas de livre demandé, montrer un rapport général
else {
  $base->biblio( array( "no", "date", "title" ) );
}
            ?>
            <a id="gotop" href="#top">▲</a>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="<?= $teinte ?>Teinte.js">//</script>
    <script type="text/javascript" src="<?= $teinte ?>Tree.js">//</script>
    <script type="text/javascript" src="<?= $teinte ?>Sortable.js">//</script>
  </body>
</html>
