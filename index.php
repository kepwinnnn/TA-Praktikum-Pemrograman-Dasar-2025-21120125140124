<?php
class MusicPlayer {
    private $songs = [];   // Encapsulated variable
    private $current = null;

    public function __construct(){
        // Manual song list in the SAME folder as index.php
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

// IF / ELSE
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
<title>Simple Music Player</title>
<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
    padding-bottom:160px; /* more space for big player bar */
}
.box{
    background:#222;
    padding:12px;
    border-radius:6px;
    margin-bottom:12px;
}
.track a{
    text-decoration:none;
    color:#ccc;
}
.track a:hover{
    color:white;
}

/* BIG FIXED PLAYER BAR */
.now{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#000;
    padding:24px;
    box-shadow:0 -4px 12px rgba(0,0,0,0.6);
}

.now-title{
    font-size:20px;
    font-weight:bold;
    margin-bottom:14px;
    text-align:center;
}

.now audio{
    width:100%;
    height:40px;
}
</style>
</head>
<body>

<h1>Music Player</h1>
<?php if($message): ?><div><?=$message?></div><?php endif; ?>

<div class="box">
    <h2>Song List</h2>
    <?php foreach($player->getSongs() as $s): ?>
        <?php
            $url = urlencode($s['url']);
            $title = urlencode($s['title']);
        ?>
        <div class="track">
            <a href="?play=<?=$url?>&title=<?=$title?>">
                Play: <?=$s['title']?>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php if($current): ?>
<div class="now">
    <div class="now-title"><?=$current['title']?></div>
    <audio controls autoplay src="<?=$current['url']?>"></audio>
</div>
<?php endif; ?>

</body>
</html>
