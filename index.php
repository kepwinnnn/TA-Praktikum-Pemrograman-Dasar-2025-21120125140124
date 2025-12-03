<?php

class MusicPlayer {
    private $songs = [];
    private $current = null;

    public function __construct(){
        $this->songs = [
            ['title' => 'Tarot - Feast', 'url' => 'Tarot.flac'],
            ['title' => 'O, Tuan - Feast', 'url' => 'o,Tuan.flac'],
            ['title' => 'From Eden - Hozier', 'url' => 'Hozier - From Eden.flac'],
            ['title' => 'Telenovia - Reality Club', 'url' => '1. Reality Club - Telenovia.flac'],
            ['title' => 'Ripples Of Past Reverie - Hoyomix', 'url' => '01. Ripples of Past Reverie (English Ver.).flac'],
            ['title' => 'Shape Of My Heart', 'url' => 'Backstreet Boys - Shape of My Heart.flac'],
            ['title' => 'Through Fire And The Flame', 'url' => '01 - DragonForce - Through The Fire And Flames.flac'],
            ['title' => 'Mood - Ian Dior', 'url' => '24kGoldn, iann dior - Mood (feat. iann dior)(Explicit).flac'],
            ['title' => 'As Long As You Love Me', 'url' => 'Backstreet Boys - As Long as You Love Me.flac'],
            ['title' => 'End Of Beginning', 'url' => 'Djo - End of Beginning.flac'],
            ['title' => 'Get Lucky', 'url' => 'Daft_Punk_Get_Lucky_feat_Pharrell_Williams_and_Nile_Rodgers.flac'],
            ['title' => 'Multo', 'url' => '07. Cup of Joe - Multo.flac'],
            ['title' => 'Aku Tenang', 'url' => 'Fourtwnty - Aku Tenang.flac'],
            ['title' => 'Diam Diam Kubawa 1', 'url' => 'Fourtwnty - Diam Diam Ku Bawa 1.flac'],
            ['title' => 'Hitam Putih', 'url' => 'Fourtwnty - Hitam Putih.flac'],
            ['title' => 'Argumentasi Dimensi', 'url' => 'Fourtwnty - Argumentasi Dimensi.flac'],
            ['title' => 'Iritasi Ringan', 'url' => 'Fourtwnty - Iritasi Ringan.flac'],
            ['title' => 'Fana Merah Jambu', 'url' => 'Fourtwnty - Fana Merah Jambu.flac'],
            ['title' => 'Puisi Alam', 'url' => 'Fourtwnty - Puisi Alam.flac'],
            ['title' => 'Aku Bukan Binatang', 'url' => 'Fourtwnty - Aku Bukan Binatang.flac'],
            ['title' => 'Diskusi Senja', 'url' => 'Fourtwnty - Diskusi Senja.flac'],
            ['title' => 'Berdansalah, Karir Ini Tak Ada Artinya ', 'url' => 'Hindia - Berdansalah, Karir Ini Tak Ada Artinya.flac'],
            ['title' => 'Kita Ke Sana', 'url' => 'Hindia - Kita Ke Sana.flac'],
            ['title' => 'Membasuh', 'url' => 'Hindia - Membasuh.flac'],
            ['title' => 'Last Surprise', 'url' => 'Lyn - Last Surprise -Scramble-.flac'],
            ['title' => 'Lagunya Begini , Nadanya Begitu', 'url' => 'Jason Ranti - Lagunya Begini, Nadanya Begitu.flac'],
            ['title' => 'Tikus Tikus Kantor', 'url' => 'Iwan Fals - Tikus - Tikus Kantor.flac'],
            ['title' => 'Sore Tugu Pancoran', 'url' => 'Iwan Fals - Sore Tugu Pancoran.flac'],
            ['title' => 'Surti Tejo', 'url' => 'Jamrud - Surti Tejo (Explicit).flac'],
            ['title' => 'Sugali', 'url' => 'Iwan Fals - Sugali.flac'],
        ];
    }

    public function getSongs(){
        return $this->songs;
    }

    public function playSong($song){
        $this->current = $song;
    }

    public function getCurrent(){
        return $this->current;
    }
}

$player = new MusicPlayer();
$message = "";

// handle play request
if (isset($_GET['play'])) {
    $song = [
        'title' => $_GET['title'],
        'url'   => $_GET['play']
    ];
    $player->playSong($song);
    $message = "Playing now: " . $_GET['title'];
}

// search handling
$query = '';
$filteredSongs = $player->getSongs();
if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    if ($query !== '') {
        $filteredSongs = array_filter($filteredSongs, function($s) use ($query) {
            return stripos($s['title'], $query) !== false;
        });
    }
}

$current = $player->getCurrent();
?>
<!doctype html>
<html>
<head>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<meta charset="utf-8">
<title>TA Progdas Kevin</title>
<style>
body{
    background:#111;
    color:white;
    font-family:'Montserrat', sans-serif;
    padding-bottom:140px;
    margin:0;
}

h1{
    letter-spacing:1px;
    font-weight:300;
    color:#eee;
    margin:20px;
}

.box{
    background:#1a1a1a;
    padding:18px;
    border-radius:10px;
    margin:20px;
    box-shadow:0 0 12px rgba(0,0,0,0.3);
}

.track{
    background:#1b1b1b;
    padding:12px;
    margin-bottom:10px;
    border-radius:8px;
    transition:0.2s;
}
.track:hover{
    background:#2d2d2d;
    transform:translateX(4px);
}
.track a{
    text-decoration:none;
    color:#ddd;
}
.track a:hover{
    color:#fff;
}

.now{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#111;
    padding:18px;
    display:flex;
    align-items:center;
    gap:18px;
    box-shadow:0 -4px 20px rgba(0,0,0,0.7);
}

.now img.cover{
    width:80px;
    height:80px;
    border-radius:6px;
    object-fit:cover;
}

.now-title{
    font-size:18px;
    font-weight:600;
}

.audio-wrap{
    flex: 1;
    display: flex;
    justify-content: flex-end;
}

.audio-wrap audio{
    width: 100%;
    max-width: 100%;
    border-radius:8px;
}

/* search box top-left */
.search{
    position:flex;
    top:18px;
    left:18px;
    display:flex;
    gap:8px;
    align-items:center;
    z-index:50;
}
.search input[type="text"]{
    padding:8px 10px;
    border-radius:6px;
    border:0;
    background:#222;
    color:#fff;
    min-width:200px;
    outline:none;
}
.search button{
    padding:8px 10px;
    border-radius:6px;
    border:0;
    background:#4a90e2;
    color:#fff;
    cursor:pointer;
}
.search button:hover{
    background:#357ab8;
}
</style>
</head>
<body>

<h1>TA Progdas Kevin 21120125140124</h1>

<!-- search form -->
<form class="search" method="get" action="">
    <input type="text" name="q" placeholder="Search songs..." value="<?=htmlspecialchars($query, ENT_QUOTES)?>">
    <button type="submit">Search</button>
</form>

<?php if($message): ?><div style="margin:20px"><?=$message?></div><?php endif; ?>

<div class="box">
    <h2>List Lagu</h2>
    <?php foreach($filteredSongs as $s): ?>
        <?php
            $url = urlencode($s['url']);
            $title = urlencode($s['title']);
            $qparam = $query ? '&q='.urlencode($query) : '';
        ?>
        <div class="track">
            <a href="?play=<?=$url?>&title=<?=$title?><?=$qparam?>">
                ▶ <?=$s['title']?>
            </a>
        </div>
    <?php endforeach; ?>

    <?php if(empty($filteredSongs)): ?>
        <div style="color:#bbb; margin-top:10px">No songs found.</div>
    <?php endif; ?>
</div>

<?php if($current): ?>
<div class="now">

    <img class="cover" src="ab67616d00001e0249bdf0e981cbba25d48b44e0ab67616d00001e02cba6a8de759fb21242c81771ab67616d00001e02d106d01a4ac447548600132eab67616d00001e02d623688488865906052ef96b.jpg">

    <div class="now-title"><?=$current['title']?></div>

    <div class="audio-wrap">
        <audio controls autoplay src="<?=$current['url']?>"></audio>
    </div>

</div>
<?php endif; ?>

</body>
</html>