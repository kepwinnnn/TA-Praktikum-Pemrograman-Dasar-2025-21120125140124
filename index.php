<?php
// ====== SONG SCANNER ======
function scan_songs($dir){
    $out = [];
    if (!is_dir($dir)) return $out;

    foreach (scandir($dir) as $f){
        if ($f==='.' || $f==='..') continue;
        $path = $dir.'/'.$f;

        if (is_dir($path)){
            $cover = '';
            $files = scandir($path);

            // find cover
            foreach ($files as $ff){
                $lower = strtolower($ff);
                if (in_array($lower,['cover.jpg','cover.png','album.jpg','album.png'])){
                    $cover = 'songs/'.rawurlencode($f).'/'.rawurlencode($ff);
                    break;
                }
            }

            // fallback: first image
            if (!$cover){
                foreach($files as $ff){
                    if (preg_match('/\.(jpg|jpeg|png)$/i',$ff)){
                        $cover = 'songs/'.rawurlencode($f).'/'.rawurlencode($ff);
                        break;
                    }
                }
            }

            // gather tracks
            $tracks = [];
            foreach($files as $ff){
                if (preg_match('/\.(flac|mp3|wav)$/i',$ff)){

                    // CLEAN TITLE FIX (complete)
                    $clean = pathinfo($ff, PATHINFO_FILENAME);

                    // remove leading dots, spaces, numbers, dash
                    $clean = preg_replace('/^[\.\s0-9-]+/', '', $clean);

                    // fix double spaces
                    $clean = preg_replace('/\s+/', ' ', $clean);

                    $tracks[] = [
                        'title' => trim($clean),
                        'url'   => 'songs/'.rawurlencode($f).'/'.rawurlencode($ff)
                    ];
                }
            }

            usort($tracks, fn($a,$b)=>strcasecmp($a['title'],$b['title']));

            if (count($tracks)){
                $out[] = [
                    'album'=>$f,
                    'cover'=>$cover,
                    'tracks'=>$tracks
                ];
            }
        }
    }

    return $out;
}

$library = scan_songs(__DIR__.'/songs');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Student Music Player</title>

<style>
:root { --bg:#0b0b0d; --panel:#0f1113; --muted:#9aa0a6; --white:#ffffff; }
html,body { margin:0; background:var(--bg); color:var(--white); font-family:Inter,Segoe UI,Arial; }
.app { display:flex; gap:12px; padding:16px; height:calc(100vh - 60px); box-sizing:border-box; }
.left { width:260px; background:var(--panel); border-radius:8px; padding:12px; overflow:auto; }
.album { display:flex; gap:10px; padding:8px; border-radius:6px; cursor:pointer; }
.album:hover { background:#141416; }
.cover img { width:60px; height:60px; object-fit:cover; border-radius:4px; }
.center { flex:1; display:flex; flex-direction:column; gap:12px; }
.tracks { background:var(--panel); border-radius:8px; flex:1; overflow:auto; padding:10px; }
.track { display:flex; gap:10px; align-items:center; padding:7px; border-radius:6px; color:var(--muted); }
.track:hover { background:#131518; color:var(--white); }
.btn { border:1px solid #2d2f31; background:transparent; color:var(--white); padding:6px 10px; border-radius:6px; cursor:pointer; }
.seekbar { position:fixed; left:0; right:0; bottom:0; display:flex; gap:10px; background:#0009; padding:10px; }
.progress { width:100%; height:6px; background:#2b2b2b; }
.icon { width:18px; height:18px; fill:white; }
</style>

</head>
<body>

<div class="app">
    <div class="left">
        <div class="small muted">ALBUMS</div>

        <?php foreach($library as $alb): ?>
        <div class="album">
            <div class="cover">
                <?php if($alb['cover']): ?>
                    <img src="<?=htmlspecialchars($alb['cover'])?>">
                <?php endif; ?>
            </div>
            <div>
                <div><?=$alb['album']?></div>
                <div class="small muted"><?=count($alb['tracks'])?> tracks</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="center">
        <div class="tracks" id="tracksBox"></div>
    </div>
</div>

<audio id="player" preload="metadata"></audio>

<div class="seekbar">
    <button id="prevBtn" class="btn">
        <svg class="icon" viewBox="0 0 24 24"><path d="M6 6v12l8.5-6zM18 6v12h-2V6z"/></svg>
    </button>

    <button id="playBtn" class="btn">
        <svg id="playIcon" class="icon" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
    </button>

    <input id="progress" type="range" class="progress" value="0">

    <button id="nextBtn" class="btn">
        <svg class="icon" viewBox="0 0 24 24"><path d="M16 6v12l-8.5-6zM6 6v12h2V6z"/></svg>
    </button>

    <div id="nowPlaying" style="width:150px;text-align:right" class="small muted">
        Not playing
    </div>
</div>

<script>
const library = <?php echo json_encode($library); ?>;

let playlist=[];
library.forEach(alb=>{
    alb.tracks.forEach(t=>{
        playlist.push({
            album: alb.album,
            title: t.title,
            url: t.url
        });
    });
});

const box=document.getElementById('tracksBox');
playlist.forEach((t,i)=>{
    const d=document.createElement('div');
    d.className='track';
    d.innerHTML = `
        <strong>${t.title}</strong>
        <span style="margin-left:auto">${i+1}</span>
    `;
    d.onclick=()=>playIndex(i);
    box.appendChild(d);
});

const player=document.getElementById('player');
const playBtn=document.getElementById('playBtn');
const playIcon=document.getElementById('playIcon');
const progress=document.getElementById('progress');
const nowPlaying=document.getElementById('nowPlaying');
let index=-1;

function playIndex(i){
    index=i;
    player.src=playlist[i].url;
    player.play();
    nowPlaying.textContent=playlist[i].title;
    playIcon.innerHTML='<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';
}

playBtn.onclick=()=>{
    if (!player.src){
        playIndex(0);
    } else if (player.paused){
        player.play();
        playIcon.innerHTML='<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';
    } else {
        player.pause();
        playIcon.innerHTML='<path d="M8 5v14l11-7z"/>';
    }
};

player.onended=()=>{
    if(index+1<playlist.length) playIndex(index+1);
};

player.ontimeupdate=()=>{
    if(player.duration){
        progress.max=player.duration;
        progress.value=player.currentTime;
    }
};

progress.oninput=()=>player.currentTime=progress.value;
</script>

</body>
</html>
