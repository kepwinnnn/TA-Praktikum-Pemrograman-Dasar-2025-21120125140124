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
            ['title' => 'Ripples Of Past Reverie', 'url' => '01. Ripples of Past Reverie (English Ver.).flac']
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

if (isset($_GET['play'])) {
    $song = [
        'title' => $_GET['title'],
        'url'   => $_GET['play']
    ];
    $player->playSong($song);
    $message = "Playing now: " . $_GET['title'];
}

$current = $player->getCurrent();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>TA Progdas Kevin</title>
<style>
body{
    background:#111;
    color:white;
    font-family:Arial, sans-serif;
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

/* ————————————————————————————————
    FIX: Seekbar full width beside title
——————————————————————————————— */
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
</style>
</head>
<body>

<h1>TA Progdas Kevin 21120125140124</h1>
<?php if($message): ?><div style="margin:20px"><?=$message?></div><?php endif; ?>

<div class="box">
    <h2>List Lagu</h2>
    <?php foreach($player->getSongs() as $s): ?>
        <?php
            $url = urlencode($s['url']);
            $title = urlencode($s['title']);
        ?>
        <div class="track">
            <a href="?play=<?=$url?>&title=<?=$title?>">
                ▶ <?=$s['title']?>
            </a>
        </div>
    <?php endforeach; ?>
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
