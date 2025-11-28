<?php
session_start();

// Pemenuhan Modul OOP/Modul5
class MusicPlayer {
    private $library = [];   // songs library
    private $queue   = [];   // queue = FIFO
    private $history = [];   // history = stack (LIFO)

    public function __construct($dir){
        $this->library = $this->scanSongs($dir);
        $this->queue   = array_map([$this,'normalizeTrack'], $_SESSION['queue']   ?? []);
        $this->history = array_map([$this,'normalizeTrack'], $_SESSION['history'] ?? []);
    }

    // Ini function + encapsulation/Modul4 + Modul6
    private function scanSongs($dir){
        $out = [];
        if (!is_dir($dir)) return $out;

        foreach (scandir($dir) as $f){
            if ($f==='.' || $f==='..') continue;
            $path = $dir.'/'.$f;
            if (!is_dir($path)) continue;

            $cover = '';
            $files = scandir($path);

            foreach ($files as $ff){
                $low = strtolower($ff);
                if (in_array($low, ['cover.jpg','cover.png','album.jpg','album.png'])){
                    $cover = "songs/".rawurlencode($f)."/".rawurlencode($ff);
                    break;
                }
            }
            if (!$cover){
                foreach ($files as $ff){
                    if (preg_match('/\.(jpg|jpeg|png)$/i',$ff)){
                        $cover = "songs/".rawurlencode($f)."/".rawurlencode($ff);
                        break;
                    }
                }
            }

            $tracks = [];
            foreach ($files as $ff){
                if (preg_match('/\.(mp3|wav|flac)$/i',$ff)){
                    $name = pathinfo($ff, PATHINFO_FILENAME);
                    $name = preg_replace('/^[\.\s0-9-]+/', '', $name);
                    $name = preg_replace('/\s+/', ' ', $name);
                    $tracks[] = [
                        'title'=>trim($name),
                        'url'  =>"songs/".rawurlencode($f)."/".rawurlencode($ff),
                        'cover'=>$cover ?: 'https://via.placeholder.com/120?text=No+Cover',
                        'album'=>$f
                    ];
                }
            }
            if (count($tracks)){
                $out[] = ['album'=>$f,'cover'=>$cover,'tracks'=>$tracks];
            }
        }
        return $out;
    }

    
    private function normalizeTrack($item){
        if (is_array($item)){
            $item['title'] = $item['title'] ?? $this->prettyTitle($item['url'] ?? '');
            $item['cover'] = $item['cover'] ?? 'https://via.placeholder.com/120?text=No+Cover';
            return $item;
        }
        return [
            'url'=>$item,
            'title'=>$this->prettyTitle($item),
            'cover'=>'https://via.placeholder.com/120?text=No+Cover'
        ];
    }

    public function prettyTitle($path){
        $base = basename(rawurldecode($path));
        $base = preg_replace('/\.(mp3|wav|flac)$/i','', $base);
        $base = preg_replace('/^[\.\s0-9-]+/', '', $base);
        $base = preg_replace('/\s+/', ' ', $base);
        return trim($base);
    }

    public function getLibrary(){ return $this->library; }
    public function getQueue(){ return $this->queue; }
    public function getHistory(){ return $this->history; }

    public function addToQueue($track){ $this->queue[] = $this->normalizeTrack($track); }
    public function nextFromQueue(){ return empty($this->queue) ? null : array_shift($this->queue); }
    public function pushHistory($track){ $this->history[] = $this->normalizeTrack($track); }
    public function popHistory(){ return empty($this->history) ? null : array_pop($this->history); }
    public function saveState(){
        $_SESSION['queue'] = $this->queue;
        $_SESSION['history'] = $this->history;
    }
}

// Ini Variable/Modul1
$player = new MusicPlayer(__DIR__.'/songs');
$play   = null;
$playTitle = "";
$playCover = "";
$message = "";

// Ini If-else/Modul2
if (isset($_GET['add'])){
    $player->addToQueue([
        'url'=>$_GET['add'],
        'title'=>isset($_GET['title']) ? urldecode($_GET['title']) : '',
        'cover'=>$_GET['cover'] ?? ''
    ]);
    $message = "Added to queue.";
}
else if (isset($_GET['next'])){
    $next = $player->nextFromQueue();
    if ($next){
        $play = $next['url'];
        $playTitle = $next['title'];
        $playCover = $next['cover'];
        $player->pushHistory($next);
        $message = "Playing next from queue.";
    } else {
        $message = "Queue empty.";
    }
}
else if (isset($_GET['play'])){
    $play = $_GET['play'];
    $playTitle = isset($_GET['title']) ? urldecode($_GET['title']) : $player->prettyTitle($play);
    $playCover = $_GET['cover'] ?? '';
    $player->pushHistory(['url'=>$play,'title'=>$playTitle,'cover'=>$playCover]);
    $message = "Playing now.";
}


$player->saveState();

// Ini GUI/Modul8
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Simple PHP Music Player</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body{margin:0;background:#0b0b0d;color:white;font-family:Montserrat;padding:20px 20px 200px 20px;}
h2{margin-top:18px;}
.box{background:#111;padding:12px;border-radius:6px;margin-bottom:12px;}
.track a{color:#9aa0a6;text-decoration:none;display:block;padding:6px 0;}
.track a:hover{color:white;}
.album{display:flex;gap:10px;margin:8px 0;}
.album img{width:60px;height:60px;object-fit:cover;border-radius:4px;}
.now{display:flex;gap:16px;align-items:center;position:fixed;left:0;right:0;bottom:0;background:#111;padding:14px 20px;border-top:1px solid #222;box-shadow:0 -6px 20px rgba(0,0,0,0.35);z-index:10;}
.now img{width:100px;height:100px;object-fit:cover;border-radius:6px;}
audio{width:100%;margin-top:12px;}
.msg{color:#66d9ef;margin:6px 0;}
.small{color:#9aa0a6;font-size:13px;}
</style>
</head>
<body>
<h1>Music Player</h1>
<?php if($message): ?><div class="msg"><?=$message?></div><?php endif; ?>

<!-- Ini buat menuhin For Loop/Modul 3-->
<div class="box">
    <h2>All Tracks</h2>
    <?php
    $lib = $player->getLibrary();
    for ($i=0; $i < count($lib); $i++){ 
        $alb = $lib[$i];
        foreach($alb['tracks'] as $t){
            $url = urlencode($t['url']);
            $title = urlencode($t['title']);
            $cover = urlencode($t['cover']);
            echo "<div class='track'>
                    <a href='?play=$url&title=$title&cover=$cover'>Play: {$t['title']} ({$alb['album']})</a>
                    <a class='small' href='?add=$url&title=$title&cover=$cover'>+ Queue</a>
                  </div>";
        }
    }
    ?>
</div>
<!-- Ini Queue Dsni Cuman Buat Menuhin Queue/FIFO Modul7  -->
<div class="box"> 
    <h2>Queue </h2> 
    <?php if (count($player->getQueue())): ?>
        <?php foreach($player->getQueue() as $q): ?>
            <?php $qt = $q['title'] ?? $player->prettyTitle($q['url'] ?? ''); ?>
            <div class="track small"><?=$qt?></div>
        <?php endforeach; ?>
        <a href="?next=1" style="color:white;">Play Next</a>
    <?php else: ?>
        <div class="small">Queue empty</div>
    <?php endif; ?>
</div>
<!-- Ini Stack Dsni Cuman Buat Menuhin Stack/LIFO Modul7  -->
<div class="box">
    <h2>Recently Played</h2>
    <?php if (count($player->getHistory())): ?>
        <?php foreach(array_reverse($player->getHistory()) as $h): ?>
            <?php $ht = $h['title'] ?? $player->prettyTitle($h['url'] ?? ''); ?>
            <div class="track small"><?=$ht?></div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="small">No history</div>
    <?php endif; ?>
</div>

<div class="box">
    <h2>Albums</h2>
    <?php foreach($lib as $alb): ?>
        <div class="album">
            <img src="<?=$alb['cover'] ?: 'https://via.placeholder.com/60?text=No+Cover'?>">
            <div>
                <div><?=$alb['album']?></div>
                <div class="small"><?=count($alb['tracks'])?> tracks</div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if($play): ?>
<div class="now">
    <img src="<?=$playCover ?: 'https://via.placeholder.com/120?text=Playing'?>">
    <div>
        <strong><?=$playTitle ?: $player->prettyTitle($play)?></strong>
        <audio id="player" controls autoplay src="<?=$play?>"></audio>
    </div>
</div>
<?php endif; ?>
</body>
</html>
