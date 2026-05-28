<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, viewport-fit=cover">
<title>EXFIL // ZERO</title>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="EXFIL">
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#040a0f">
<style>
@import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');
:root{--neon:#00ffe7;--red:#ff2a2a;--gold:#ffd700;--dark:#040a0f;--panel:rgba(0,20,30,0.92);--border:rgba(0,255,231,0.3);}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent;-webkit-touch-callout:none;}
html,body{width:100%;height:100%;overflow:hidden;background:#040a0f;touch-action:none;user-select:none;-webkit-user-select:none;}
body{font-family:'Share Tech Mono',monospace;color:var(--neon);padding:env(safe-area-inset-top,0px) env(safe-area-inset-right,0px) env(safe-area-inset-bottom,0px) env(safe-area-inset-left,0px);}
#gameCanvas{position:fixed;top:0;left:0;display:block;}
.overlay{position:fixed;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:radial-gradient(ellipse at center,#001a24 0%,#040a0f 70%);z-index:100;overflow-y:auto;}
.logo{font-family:'Orbitron',sans-serif;font-weight:900;font-size:clamp(1.8rem,7vw,4rem);letter-spacing:0.2em;color:var(--neon);text-shadow:0 0 20px var(--neon),0 0 60px rgba(0,255,231,0.4);animation:pulse 2s ease-in-out infinite;}
.tagline{font-size:0.8rem;letter-spacing:0.5em;color:rgba(0,255,231,0.5);margin:0.3rem 0 2rem;}
@keyframes pulse{0%,100%{text-shadow:0 0 20px var(--neon),0 0 60px rgba(0,255,231,0.4);}50%{text-shadow:0 0 40px var(--neon),0 0 100px rgba(0,255,231,0.6),0 0 3px #fff;}}
.btn{background:transparent;border:1px solid var(--neon);color:var(--neon);font-family:'Share Tech Mono',monospace;font-size:0.9rem;letter-spacing:0.2em;padding:0.7rem 1.8rem;cursor:pointer;margin:0.3rem;transition:all 0.2s;text-transform:uppercase;min-width:200px;position:relative;overflow:hidden;}
.btn::before{content:'';position:absolute;inset:0;background:var(--neon);transform:translateX(-101%);transition:transform 0.2s;z-index:-1;}
.btn:active::before{transform:translateX(0);}
.btn:active{color:var(--dark);}
.btn.danger{border-color:var(--red);color:var(--red);}
.btn.danger::before{background:var(--red);}
.btn.gold{border-color:var(--gold);color:var(--gold);}
.btn.gold::before{background:var(--gold);}
input[type=text]{background:rgba(0,255,231,0.05);border:1px solid var(--border);color:var(--neon);font-family:'Share Tech Mono',monospace;font-size:0.9rem;padding:0.6rem 1rem;width:230px;letter-spacing:0.1em;margin:0.3rem;outline:none;text-align:center;}
input[type=text]:focus{border-color:var(--neon);}
input::placeholder{color:rgba(0,255,231,0.3);}
#hud{position:fixed;top:0;left:0;right:0;padding:8px 12px;display:none;align-items:flex-start;justify-content:space-between;pointer-events:none;z-index:10;}
.hud-panel{background:var(--panel);border:1px solid var(--border);padding:6px 10px;min-width:110px;}
.hud-label{font-size:0.55rem;letter-spacing:0.3em;color:rgba(0,255,231,0.5);margin-bottom:2px;}
.hud-value{font-family:'Orbitron',sans-serif;font-size:0.9rem;font-weight:700;}
#healthBar{width:100%;height:3px;background:rgba(255,42,42,0.2);margin-top:3px;}
#healthFill{height:100%;background:var(--red);transition:width 0.2s;box-shadow:0 0 6px var(--red);}
#ammoDisplay{color:var(--gold);}
/* Weapon selector HUD */
#weaponBar{position:fixed;bottom:calc(155px + env(safe-area-inset-bottom,0px));left:50%;transform:translateX(-50%);display:none;gap:6px;z-index:20;pointer-events:none;}
.wslot{width:52px;height:52px;border:1px solid var(--border);background:var(--panel);display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:0.6rem;letter-spacing:0.05em;color:rgba(0,255,231,0.5);position:relative;}
.wslot.active{border-color:var(--neon);box-shadow:0 0 10px rgba(0,255,231,0.4);}
.wslot .wicon{font-size:1.1rem;margin-bottom:1px;}
.wslot .wkey{position:absolute;top:2px;left:4px;font-size:0.5rem;color:var(--gold);}
#lootPopup{position:fixed;bottom:calc(220px + env(safe-area-inset-bottom,0px));left:50%;transform:translateX(-50%);background:var(--panel);border:1px solid var(--gold);color:var(--gold);padding:6px 16px;font-size:0.78rem;letter-spacing:0.1em;z-index:20;opacity:0;transition:opacity 0.3s;pointer-events:none;white-space:nowrap;}
#minimap{position:fixed;top:10px;right:10px;width:110px;height:110px;border:1px solid var(--border);background:rgba(0,10,15,0.8);z-index:10;display:none;}
#extractionTimer{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);font-family:'Orbitron',sans-serif;font-size:1.6rem;color:var(--gold);text-shadow:0 0 20px var(--gold);z-index:30;display:none;text-align:center;}
#joystickLeft,#joystickRight{position:fixed;bottom:calc(25px + env(safe-area-inset-bottom,0px));width:110px;height:110px;border-radius:50%;background:rgba(0,255,231,0.06);border:2px solid rgba(0,255,231,0.25);z-index:50;touch-action:none;display:none;}
#joystickLeft{left:25px;}
#joystickRight{right:25px;}
.joystick-thumb{position:absolute;width:44px;height:44px;border-radius:50%;background:radial-gradient(circle,rgba(0,255,231,0.7),rgba(0,255,231,0.2));border:2px solid var(--neon);top:50%;left:50%;transform:translate(-50%,-50%);box-shadow:0 0 12px rgba(0,255,231,0.5);}
/* Weapon switch button (mobile) */
#switchWepBtn{position:fixed;bottom:calc(25px + env(safe-area-inset-bottom,0px));left:50%;transform:translateX(-50%);width:60px;height:60px;border-radius:50%;background:rgba(255,215,0,0.1);border:1px solid var(--gold);color:var(--gold);font-size:0.6rem;letter-spacing:0.05em;z-index:50;display:none;align-items:center;justify-content:center;flex-direction:column;cursor:pointer;}
#switchWepBtn .swicon{font-size:1.2rem;}
#killfeed{position:fixed;right:130px;top:10px;z-index:20;pointer-events:none;}
.kill-entry{background:rgba(255,42,42,0.15);border-left:2px solid var(--red);padding:3px 8px;font-size:0.7rem;margin-bottom:3px;animation:fadeIn 0.2s;}
@keyframes fadeIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:none}}
#connStatus{position:fixed;bottom:calc(150px + env(safe-area-inset-bottom,0px));right:12px;font-size:0.65rem;letter-spacing:0.1em;color:rgba(0,255,231,0.5);z-index:20;display:none;}
.dot{display:inline-block;width:6px;height:6px;border-radius:50%;background:#888;margin-right:4px;}
.dot.green{background:#00ff88;box-shadow:0 0 6px #00ff88;}
.dot.yellow{background:var(--gold);}
.dot.red{background:var(--red);}
.go-title{font-family:'Orbitron',sans-serif;font-size:clamp(1.8rem,7vw,3rem);font-weight:900;margin-bottom:0.8rem;}
.go-title.dead{color:var(--red);text-shadow:0 0 30px var(--red);}
.go-title.win{color:var(--gold);text-shadow:0 0 30px var(--gold);}
.lobby-code{font-family:'Orbitron',sans-serif;font-size:1.6rem;color:var(--gold);letter-spacing:0.4em;padding:0.7rem 1.5rem;border:1px solid var(--gold);margin:0.7rem 0;text-shadow:0 0 15px var(--gold);}
.lobby-info{font-size:0.75rem;color:rgba(0,255,231,0.6);margin:0.3rem 0;letter-spacing:0.15em;}
.player-entry{padding:3px 16px;border-left:2px solid var(--neon);margin:3px 0;font-size:0.85rem;}
.scanline{position:fixed;inset:0;background:repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(0,0,0,0.07) 2px,rgba(0,0,0,0.07) 4px);pointer-events:none;z-index:999;}
</style>
</head>
<body>
<div class="scanline"></div>
<canvas id="gameCanvas"></canvas>

<div id="mainMenu" class="overlay">
  <div class="logo">EXFIL // ZERO</div>
  <div class="tagline">Extract or Die Trying</div>
  <input type="text" id="nameInput" placeholder="CALL SIGN" maxlength="12">
  <button class="btn gold" onclick="createLobby()">HOST GAME</button>
  <button class="btn" onclick="showJoinScreen()">JOIN GAME</button>
  <button class="btn" onclick="startSolo()">SOLO RUN</button>
  <div id="joinSection" style="display:none;flex-direction:column;align-items:center;margin-top:0.4rem;">
    <input type="text" id="codeInput" placeholder="ROOM CODE" maxlength="6" style="text-transform:uppercase">
    <button class="btn" onclick="joinLobby()">CONNECT</button>
  </div>
</div>

<div id="lobbyScreen" class="overlay" style="display:none">
  <div class="logo" style="font-size:1.6rem">STAGING AREA</div>
  <div class="lobby-info">SHARE THIS CODE</div>
  <div class="lobby-code" id="lobbyCode">------</div>
  <div class="lobby-info" id="lobbyStatusText">WAITING...</div>
  <div id="playerList"></div>
  <button class="btn gold" id="startBtn" style="display:none" onclick="hostStartGame()">DEPLOY</button>
  <button class="btn danger" onclick="leaveLobby()">ABORT</button>
</div>

<div id="hud">
  <div class="hud-panel">
    <div class="hud-label">HEALTH</div>
    <div class="hud-value" id="healthVal" style="color:var(--red)">100</div>
    <div id="healthBar"><div id="healthFill" style="width:100%"></div></div>
  </div>
  <div class="hud-panel" style="text-align:center">
    <div class="hud-label">LOOT</div>
    <div class="hud-value" style="color:var(--gold)" id="lootVal">0</div>
    <div class="hud-label" style="margin-top:2px">ZONE</div>
    <div class="hud-value" id="zoneTimer" style="font-size:0.8rem">--:--</div>
  </div>
  <div class="hud-panel" style="text-align:right">
    <div class="hud-label">AMMO</div>
    <div class="hud-value" id="ammoDisplay">30/90</div>
    <div class="hud-label" style="margin-top:2px">KILLS</div>
    <div class="hud-value" id="killCount">0</div>
  </div>
</div>

<!-- Weapon bar -->
<div id="weaponBar"></div>

<canvas id="minimap" width="110" height="110"></canvas>
<div id="killfeed"></div>
<div id="connStatus"><span class="dot" id="connDot"></span><span id="connText">OFFLINE</span></div>
<div id="lootPopup"></div>
<div id="extractionTimer"></div>
<div id="joystickLeft"><div class="joystick-thumb" id="thumbL"></div></div>
<div id="joystickRight"><div class="joystick-thumb" id="thumbR"></div></div>
<div id="switchWepBtn" onclick="cycleWeapon()"><span class="swicon">🔫</span>SWAP</div>

<div id="gameOverScreen" class="overlay" style="display:none">
  <div class="go-title" id="goTitle">ELIMINATED</div>
  <div style="font-size:0.9rem;letter-spacing:0.15em;margin:0.8rem 0;color:rgba(0,255,231,0.7)" id="goStats"></div>
  <button class="btn gold" onclick="goToMenu()">MAIN MENU</button>
</div>

<script>
// ══════════════════════════════════════════════════════
// EXFIL // ZERO  —  v3
// ══════════════════════════════════════════════════════
const canvas = document.getElementById('gameCanvas');
const ctx    = canvas.getContext('2d');
const mmCv   = document.getElementById('minimap');
const mmCtx  = mmCv.getContext('2d');

const TILE=40,MAP_W=64,MAP_H=64;
const PLAYER_SPEED=180;
const MAX_HEALTH=100,EXTRACTION_TIME=5000,ZONE_CLOSE_TIME=120;

// ── Weapon definitions ─────────────────────────────────
const WEAPONS = {
  pistol:  {name:'PISTOL',  icon:'🔫', ammo:12, reserve:48,  fireRate:250,  reloadTime:1200, damage:18, bulletSpd:480, spread:0.04, burstCount:1, color:'#ffffff', range:1.4},
  smg:     {name:'SMG',     icon:'⚡',  ammo:30, reserve:120, fireRate:90,   reloadTime:1600, damage:12, bulletSpd:520, spread:0.10, burstCount:1, color:'#ffdd44', range:1.2},
  shotgun: {name:'SHOTGUN', icon:'💥', ammo:6,  reserve:24,  fireRate:700,  reloadTime:2000, damage:14, bulletSpd:380, spread:0.30, burstCount:6, color:'#ff8844', range:0.7},
  rifle:   {name:'RIFLE',   icon:'🎯', ammo:20, reserve:60,  fireRate:180,  reloadTime:2200, damage:28, bulletSpd:680, spread:0.02, burstCount:1, color:'#44ffbb', range:2.0},
  sniper:  {name:'SNIPER',  icon:'🔭', ammo:5,  reserve:15,  fireRate:900,  reloadTime:2500, damage:80, bulletSpd:900, spread:0.005,burstCount:1, color:'#aa44ff', range:3.0},
};
function mkWeapon(key){const d=WEAPONS[key];return{key,name:d.name,icon:d.icon,ammo:d.ammo,reserve:d.reserve,fireRate:d.fireRate,reloadTime:d.reloadTime,damage:d.damage,bulletSpd:d.bulletSpd,spread:d.spread,burstCount:d.burstCount,color:d.color,range:d.range,reloading:false,reloadStart:0};}

// ── IndexedDB ─────────────────────────────────────────
let db;
function initDB(){return new Promise(res=>{const r=indexedDB.open('ExfilZero',3);r.onupgradeneeded=e=>{const d=e.target.result;if(!d.objectStoreNames.contains('profile'))d.createObjectStore('profile',{keyPath:'id'});if(!d.objectStoreNames.contains('runs'))d.createObjectStore('runs',{autoIncrement:true});};r.onsuccess=e=>{db=e.target.result;res();};r.onerror=()=>res();});}
function dbSet(s,v){if(!db)return;db.transaction(s,'readwrite').objectStore(s).put(v);}
function dbGet(s,k){return new Promise(res=>{if(!db)return res(null);const r=db.transaction(s,'readonly').objectStore(s).get(k);r.onsuccess=()=>res(r.result);r.onerror=()=>res(null);});}

// ── State ─────────────────────────────────────────────
let gameState='menu';
let localPlayer=null,players={},enemies=[],bullets=[],lootItems=[],particles=[];
let tileMap=[],extractionZone={x:0,y:0,r:80};
let gameTime=ZONE_CLOSE_TIME,gameTimer=null;
let myId='p_'+Math.random().toString(36).slice(2,8);
let myName='GHOST',kills=0,lootCollected=0;
let extractionProgress=0,isExtracting=false;

// ── P2P ───────────────────────────────────────────────
let isHost=false,roomCode='';
let peers={},dataChannels={},signalingWs=null,pendingCandidates={};
const SIGNAL_URL='wss://socketsbay.com/wss/v2/1/demo/';
function connectSignaling(){try{signalingWs=new WebSocket(SIGNAL_URL);signalingWs.onopen=()=>setConn('yellow','SIGNALING');signalingWs.onmessage=e=>{try{handleSignal(JSON.parse(e.data));}catch(_){}};signalingWs.onerror=()=>setConn('red','SIG ERR');signalingWs.onclose=()=>{if(gameState==='playing')setConn('red','LOST');};}catch(_){setConn('red','NO NET');}}
function sendSignal(msg){if(signalingWs&&signalingWs.readyState===1)signalingWs.send(JSON.stringify({room:roomCode,...msg}));}
async function handleSignal(msg){
  if(msg.room!==roomCode||msg.from===myId)return;
  if(msg.type==='join'){if(isHost)await createPC(msg.from,true);updateLobbyP(msg.from,msg.name);}
  if(msg.type==='offer'){await createPC(msg.from,false);await peers[msg.from].setRemoteDescription(new RTCSessionDescription(msg.sdp));const a=await peers[msg.from].createAnswer();await peers[msg.from].setLocalDescription(a);sendSignal({type:'answer',from:myId,to:msg.from,sdp:a});flushC(msg.from);}
  if(msg.type==='answer'&&peers[msg.from]){await peers[msg.from].setRemoteDescription(new RTCSessionDescription(msg.sdp));flushC(msg.from);}
  if(msg.type==='ice'&&peers[msg.from]){try{await peers[msg.from].addIceCandidate(new RTCIceCandidate(msg.candidate));}catch(_){}}
  if(msg.type==='start'&&!isHost)startGame(msg.seed);
}
async function createPC(pid,init){
  if(peers[pid])return;
  const pc=new RTCPeerConnection({iceServers:[{urls:'stun:stun.l.google.com:19302'},{urls:'stun:stun1.l.google.com:19302'}]});
  peers[pid]=pc;pendingCandidates[pid]=[];
  pc.onicecandidate=e=>{if(e.candidate)sendSignal({type:'ice',from:myId,to:pid,candidate:e.candidate});};
  pc.onconnectionstatechange=()=>{if(pc.connectionState==='connected')setConn('green','P2P OK');if(pc.connectionState==='disconnected')onPeerDrop(pid);};
  if(init){const dc=pc.createDataChannel('g',{ordered:false,maxRetransmits:0});setupDC(dc,pid);const o=await pc.createOffer();await pc.setLocalDescription(o);sendSignal({type:'offer',from:myId,to:pid,sdp:o});}
  else pc.ondatachannel=e=>setupDC(e.channel,pid);
}
function flushC(pid){(pendingCandidates[pid]||[]).forEach(c=>{try{peers[pid].addIceCandidate(new RTCIceCandidate(c));}catch(_){}});pendingCandidates[pid]=[];}
function setupDC(dc,pid){dataChannels[pid]=dc;dc.onopen=()=>{setConn('green','OK');sendP(pid,{type:'hello',id:myId,name:myName});};dc.onmessage=e=>{try{onPeerMsg(pid,JSON.parse(e.data));}catch(_){}};dc.onerror=_=>{};}
function sendP(pid,msg){const dc=dataChannels[pid];if(dc&&dc.readyState==='open')try{dc.send(JSON.stringify(msg));}catch(_){}}
function broadcast(msg){Object.keys(dataChannels).forEach(id=>sendP(id,msg));}
function onPeerMsg(pid,msg){
  if(msg.type==='hello')players[msg.id]=players[msg.id]||mkRemote(msg.id,msg.name);
  if(msg.type==='state'){if(!players[msg.id])players[msg.id]=mkRemote(msg.id,msg.name||'?');const p=players[msg.id];p.x=msg.x;p.y=msg.y;p.aimAngle=msg.a;p.health=msg.h;p.dead=msg.dead;}
  if(msg.type==='shoot')spawnBullet(msg.x,msg.y,msg.a,msg.id,msg.spd,msg.clr,msg.dmg,msg.rng);
  if(msg.type==='hit'&&msg.target===myId&&localPlayer){localPlayer.health-=msg.dmg;showHit();if(localPlayer.health<=0)playerDead(msg.killer);}
  if(msg.type==='loot_taken')lootItems=lootItems.filter(l=>l.id!==msg.lootId);
  if(msg.type==='enemy_dead'){const en=enemies.find(e=>e.id===msg.id);if(en)en.health=0;}
}
function onPeerDrop(pid){if(players[pid])players[pid].dead=true;setConn('yellow','LEFT');}

// ── Lobby ─────────────────────────────────────────────
let lobbyPlayers={};
function updateLobbyP(id,name){lobbyPlayers[id]=name;renderLobby();}
function renderLobby(){
  document.getElementById('playerList').innerHTML=Object.entries(lobbyPlayers).map(([id,n])=>`<div class="player-entry">▶ ${n}${id===myId?' (YOU)':''}</div>`).join('');
  const c=Object.keys(lobbyPlayers).length;
  document.getElementById('lobbyStatusText').textContent=`${c} OPERATOR${c!==1?'S':''} STAGED`;
  if(isHost&&c>=1)document.getElementById('startBtn').style.display='';
}
function showJoinScreen(){const s=document.getElementById('joinSection');s.style.display=s.style.display==='none'?'flex':'none';}
async function createLobby(){myName=document.getElementById('nameInput').value.trim().toUpperCase()||'GHOST';isHost=true;roomCode=Math.random().toString(36).slice(2,8).toUpperCase();lobbyPlayers={};updateLobbyP(myId,myName);document.getElementById('mainMenu').style.display='none';document.getElementById('lobbyScreen').style.display='flex';document.getElementById('lobbyCode').textContent=roomCode;connectSignaling();setTimeout(()=>sendSignal({type:'join',from:myId,name:myName}),800);}
function joinLobby(){myName=document.getElementById('nameInput').value.trim().toUpperCase()||'SHADOW';roomCode=document.getElementById('codeInput').value.trim().toUpperCase();if(!roomCode)return;isHost=false;lobbyPlayers={};updateLobbyP(myId,myName);document.getElementById('mainMenu').style.display='none';document.getElementById('lobbyScreen').style.display='flex';document.getElementById('lobbyCode').textContent=roomCode;connectSignaling();setTimeout(()=>sendSignal({type:'join',from:myId,name:myName}),800);}
function leaveLobby(){if(signalingWs)signalingWs.close();goToMenu();}
function hostStartGame(){const seed=Date.now();sendSignal({type:'start',from:myId,seed});startGame(seed);}
function startSolo(){myName=document.getElementById('nameInput').value.trim().toUpperCase()||'GHOST';document.getElementById('mainMenu').style.display='none';startGame(Date.now());}

// ── Back to menu (fix: proper reset) ──────────────────
function goToMenu(){
  gameState='menu';
  if(gameTimer){clearInterval(gameTimer);gameTimer=null;}
  if(animId){cancelAnimationFrame(animId);animId=null;}
  if(signalingWs){try{signalingWs.close();}catch(_){}}
  signalingWs=null; peers={}; dataChannels={}; pendingCandidates={};
  localPlayer=null; players={}; enemies=[]; bullets=[]; lootItems=[]; particles=[];
  lobbyPlayers={}; kills=0; lootCollected=0; gameTime=ZONE_CLOSE_TIME;
  isExtracting=false; extractionProgress=0; shootCD=0;
  ['hud','minimap','joystickLeft','joystickRight','connStatus','weaponBar','switchWepBtn'].forEach(id=>document.getElementById(id).style.display='none');
  document.getElementById('gameOverScreen').style.display='none';
  document.getElementById('mainMenu').style.display='flex';
  ctx.clearRect(0,0,canvas.width,canvas.height);
}

// ── Seeded RNG ────────────────────────────────────────
let rng=0;
function rnd(){rng=(rng*1664525+1013904223)&0xffffffff;return(rng>>>0)/0xffffffff;}

// ── Map ───────────────────────────────────────────────
function genMap(seed){
  rng=seed;tileMap=[];
  for(let y=0;y<MAP_H;y++){tileMap[y]=[];for(let x=0;x<MAP_W;x++){
    if(x===0||y===0||x===MAP_W-1||y===MAP_H-1)tileMap[y][x]=1;
    else{const r=rnd();tileMap[y][x]=r<0.12?1:r<0.18?2:0;}
  }}
  for(let i=0;i<14;i++){
    const rx=Math.floor(rnd()*(MAP_W-14))+4,ry=Math.floor(rnd()*(MAP_H-14))+4;
    const rw=Math.floor(rnd()*8)+5,rh=Math.floor(rnd()*8)+5;
    for(let dy=ry;dy<ry+rh;dy++)for(let dx=rx;dx<rx+rw;dx++)if(dy>0&&dy<MAP_H-1&&dx>0&&dx<MAP_W-1)tileMap[dy][dx]=0;
    for(let dy=ry-1;dy<=ry+rh;dy++)for(let dx=rx-1;dx<=rx+rw;dx++){if(dy<1||dy>=MAP_H-1||dx<1||dx>=MAP_W-1)continue;if((dy===ry-1||dy===ry+rh||dx===rx-1||dx===rx+rw)&&tileMap[dy][dx]===0)tileMap[dy][dx]=1;}
  }
  extractionZone={x:(MAP_W-8)*TILE,y:(MAP_H-8)*TILE,r:90};
  for(let dy=MAP_H-11;dy<MAP_H-2;dy++)for(let dx=MAP_W-11;dx<MAP_W-2;dx++)if(tileMap[dy]&&tileMap[dy][dx]!==undefined)tileMap[dy][dx]=4;
}
function solid(x,y){const tx=Math.floor(x/TILE),ty=Math.floor(y/TILE);if(tx<0||ty<0||tx>=MAP_W||ty>=MAP_H)return true;const t=tileMap[ty][tx];return t===1||t===3;}
function cover(x,y){const tx=Math.floor(x/TILE),ty=Math.floor(y/TILE);if(tx<0||ty<0||tx>=MAP_W||ty>=MAP_H)return false;return tileMap[ty][tx]===2;}

// ── Line-of-sight check (DDA raycast) ─────────────────
function hasLOS(x0,y0,x1,y1){
  // Bresenham-style tile traversal
  let tx0=Math.floor(x0/TILE),ty0=Math.floor(y0/TILE);
  const tx1=Math.floor(x1/TILE),ty1=Math.floor(y1/TILE);
  const dx=Math.abs(tx1-tx0),dy=Math.abs(ty1-ty0);
  const sx=tx0<tx1?1:-1,sy=ty0<ty1?1:-1;
  let err=dx-dy;
  for(let steps=0;steps<80;steps++){
    if(tx0===tx1&&ty0===ty1)return true;
    if(tx0<0||ty0<0||tx0>=MAP_W||ty0>=MAP_H)return false;
    const t=tileMap[ty0][tx0];
    if(t===1||t===3)return false; // wall blocks sight
    const e2=2*err;
    if(e2>-dy){err-=dy;tx0+=sx;}
    if(e2<dx){err+=dx;ty0+=sy;}
  }
  return false;
}

// ── Players ───────────────────────────────────────────
function mkLocal(){
  return{id:myId,name:myName,x:3*TILE,y:3*TILE,aimAngle:0,health:MAX_HEALTH,maxHealth:MAX_HEALTH,dead:false,radius:14,
    weapons:[mkWeapon('pistol'),mkWeapon('smg')],weaponIdx:0,
    color:'#00ffe7',isLocal:true};
}
function mkRemote(id,name){return{id,name,x:5*TILE,y:5*TILE,aimAngle:0,health:MAX_HEALTH,maxHealth:MAX_HEALTH,dead:false,radius:14,color:'#ff6b35',isLocal:false};}
function curWep(){return localPlayer?localPlayer.weapons[localPlayer.weaponIdx]:null;}

function cycleWeapon(){
  if(!localPlayer)return;
  localPlayer.weaponIdx=(localPlayer.weaponIdx+1)%localPlayer.weapons.length;
  shootCD=0;
  updWeaponBar();updHUD();
}

// Pick up weapon loot
function equipWeaponLoot(key){
  if(!localPlayer)return;
  // Check if already carried
  const existing=localPlayer.weapons.findIndex(w=>w.key===key);
  if(existing>=0){
    // Refill ammo
    const w=localPlayer.weapons[existing];
    w.reserve=Math.min(w.reserve+WEAPONS[key].reserve,WEAPONS[key].reserve*3);
    showPopup(`${WEAPONS[key].icon} AMMO REFILLED`);
    return;
  }
  // Max 3 weapons
  if(localPlayer.weapons.length>=3)localPlayer.weapons[localPlayer.weaponIdx]=mkWeapon(key);
  else localPlayer.weapons.push(mkWeapon(key));
  updWeaponBar();showPopup(`${WEAPONS[key].icon} ${WEAPONS[key].name} EQUIPPED`);
}

function updWeaponBar(){
  if(!localPlayer)return;
  const bar=document.getElementById('weaponBar');
  bar.innerHTML='';
  localPlayer.weapons.forEach((w,i)=>{
    const sl=document.createElement('div');sl.className='wslot'+(i===localPlayer.weaponIdx?' active':'');
    sl.innerHTML=`<span class="wkey">${i+1}</span><span class="wicon">${w.icon}</span><span>${w.name}</span><span style="color:var(--gold);font-size:0.65rem">${w.ammo}/${w.reserve}</span>`;
    bar.appendChild(sl);
  });
}

// ── Enemies ───────────────────────────────────────────
function mkEnemy(x,y,type='grunt'){
  const T={
    grunt: {health:60, speed:55,damage:12,radius:13,color:'#ff2a2a',reward:1,sight:250,fireRate:900},
    heavy: {health:150,speed:32,damage:22,radius:18,color:'#ff6622',reward:3,sight:200,fireRate:1400},
    sniper:{health:40, speed:28,damage:40,radius:11,color:'#aa00ff',reward:2,sight:380,fireRate:2800},
  };
  const t=T[type]||T.grunt;
  return{id:'e_'+Math.random().toString(36).slice(2),x,y,type,...t,maxHealth:t.health,
    patrolAngle:rnd()*Math.PI*2,state:'patrol',lastShot:0,alertCooldown:0,
    lastKnownX:x,lastKnownY:y};
}
function spawnEnemies(){
  enemies=[];
  for(let i=0;i<38;i++){
    let x,y,tries=0;
    do{x=(Math.floor(rnd()*(MAP_W-4))+2)*TILE+TILE/2;y=(Math.floor(rnd()*(MAP_H-4))+2)*TILE+TILE/2;tries++;}while(solid(x,y)&&tries<20);
    if(!solid(x,y)&&Math.hypot(x-localPlayer.x,y-localPlayer.y)>220){
      const r=rnd();enemies.push(mkEnemy(x,y,r<0.62?'grunt':r<0.86?'heavy':'sniper'));
    }
  }
}

function spawnLoot(){
  lootItems=[];
  const LT=[
    {type:'ammo',   icon:'🔋',value:0, color:'#ffd700',label:'AMMO PACK'},
    {type:'health', icon:'💊',value:40,color:'#00ff88',label:'+40 HP'},
    {type:'valuables',icon:'💎',value:5,color:'#a78bfa',label:'+5 LOOT'},
    {type:'bigLoot',icon:'📦',value:15,color:'#f59e0b',label:'+15 LOOT'},
    // Weapon drops
    {type:'weapon', icon:'⚡', wkey:'smg',   color:'#ffdd44',label:'SMG FOUND'},
    {type:'weapon', icon:'💥', wkey:'shotgun',color:'#ff8844',label:'SHOTGUN FOUND'},
    {type:'weapon', icon:'🎯', wkey:'rifle',  color:'#44ffbb',label:'RIFLE FOUND'},
    {type:'weapon', icon:'🔭', wkey:'sniper', color:'#aa44ff',label:'SNIPER FOUND'},
  ];
  // Always spawn at least one of each weapon type
  const wDrops=['smg','shotgun','rifle','sniper'];
  wDrops.forEach(wk=>{
    let x,y,tries=0;
    do{x=(Math.floor(rnd()*(MAP_W-6))+3)*TILE+TILE/2;y=(Math.floor(rnd()*(MAP_H-6))+3)*TILE+TILE/2;tries++;}while(solid(x,y)&&tries<30);
    if(!solid(x,y)){const ld=LT.find(l=>l.wkey===wk);lootItems.push({...ld,x,y,id:'wl_'+wk,collected:false});}
  });
  // Random loot
  for(let i=0;i<50;i++){
    let x,y,tries=0;
    do{x=(Math.floor(rnd()*(MAP_W-4))+2)*TILE+TILE/2;y=(Math.floor(rnd()*(MAP_H-4))+2)*TILE+TILE/2;tries++;}while(solid(x,y)&&tries<20);
    if(!solid(x,y)){const lt=LT[Math.floor(rnd()*4)]; // only consumables
    lootItems.push({...lt,x,y,id:'l_'+i,collected:false});}
  }
}

// ── Bullets ───────────────────────────────────────────
function spawnBullet(x,y,angle,oid,spd,clr,dmg,rng){
  bullets.push({x,y,vx:Math.cos(angle)*spd,vy:Math.sin(angle)*spd,ownerId:oid,life:rng,radius:oid.startsWith('e_')?3.5:4,color:clr,damage:dmg});
}
function spawnPart(x,y,color,count=8){for(let i=0;i<count;i++){const a=Math.random()*Math.PI*2,spd=60+Math.random()*140;particles.push({x,y,vx:Math.cos(a)*spd,vy:Math.sin(a)*spd,life:0.4+Math.random()*0.4,maxLife:0.8,radius:2+Math.random()*3,color});}}

// ── Camera ────────────────────────────────────────────
let cam={x:0,y:0};
function updCam(){if(!localPlayer)return;cam.x=Math.max(0,Math.min(localPlayer.x-canvas.width/2,MAP_W*TILE-canvas.width));cam.y=Math.max(0,Math.min(localPlayer.y-canvas.height/2,MAP_H*TILE-canvas.height));}

// ── Joysticks ─────────────────────────────────────────
const jL={active:false,startX:0,startY:0,dx:0,dy:0,id:-1};
const jR={active:false,startX:0,startY:0,dx:0,dy:0,id:-1};
const JR=55;
function initJoy(){
  const eL=document.getElementById('joystickLeft'),eR=document.getElementById('joystickRight');
  const tL=document.getElementById('thumbL'),tR=document.getElementById('thumbR');
  document.addEventListener('touchstart',e=>{
    e.preventDefault();
    for(const t of e.changedTouches){
      const rl=eL.getBoundingClientRect(),rr=eR.getBoundingClientRect();
      if(t.clientX>rl.left-35&&t.clientX<rl.right+35&&t.clientY>rl.top-35&&t.clientY<rl.bottom+35){
        jL.active=true;jL.id=t.identifier;jL.startX=rl.left+rl.width/2;jL.startY=rl.top+rl.height/2;tL.style.boxShadow='0 0 20px rgba(0,255,231,0.9)';
      }else if(t.clientX>rr.left-35&&t.clientX<rr.right+35&&t.clientY>rr.top-35&&t.clientY<rr.bottom+35){
        jR.active=true;jR.id=t.identifier;jR.startX=rr.left+rr.width/2;jR.startY=rr.top+rr.height/2;tR.style.boxShadow='0 0 20px rgba(0,255,231,0.9)';
      }
    }
  },{passive:false});
  document.addEventListener('touchmove',e=>{
    e.preventDefault();
    for(const t of e.changedTouches){
      if(jL.active&&t.identifier===jL.id){const dx=t.clientX-jL.startX,dy=t.clientY-jL.startY,d=Math.hypot(dx,dy),cl=Math.min(d,JR),a=Math.atan2(dy,dx);jL.dx=Math.cos(a)*cl/JR;jL.dy=Math.sin(a)*cl/JR;tL.style.transform=`translate(calc(-50% + ${Math.cos(a)*cl}px),calc(-50% + ${Math.sin(a)*cl}px))`;}
      if(jR.active&&t.identifier===jR.id){const dx=t.clientX-jR.startX,dy=t.clientY-jR.startY,d=Math.hypot(dx,dy),cl=Math.min(d,JR),a=Math.atan2(dy,dx);jR.dx=Math.cos(a)*cl/JR;jR.dy=Math.sin(a)*cl/JR;tR.style.transform=`translate(calc(-50% + ${Math.cos(a)*cl}px),calc(-50% + ${Math.sin(a)*cl}px))`;}
    }
  },{passive:false});
  const reset=e=>{for(const t of e.changedTouches){
    if(jL.id===t.identifier){jL.active=false;jL.dx=0;jL.dy=0;jL.id=-1;tL.style.transform='translate(-50%,-50%)';tL.style.boxShadow='0 0 12px rgba(0,255,231,0.5)';}
    if(jR.id===t.identifier){jR.active=false;jR.dx=0;jR.dy=0;jR.id=-1;tR.style.transform='translate(-50%,-50%)';tR.style.boxShadow='0 0 12px rgba(0,255,231,0.5)';}
  }};
  document.addEventListener('touchend',reset,{passive:false});
  document.addEventListener('touchcancel',reset,{passive:false});
}

// ── Input ─────────────────────────────────────────────
const keys={};
document.addEventListener('keydown',e=>{
  if(gameState!=='playing')return;
  keys[e.code]=true;
  if(e.code==='Space'||e.code==='KeyF')shoot();
  if(e.code==='KeyR')reload();
  if(e.code==='Digit1')switchTo(0);
  if(e.code==='Digit2')switchTo(1);
  if(e.code==='Digit3')switchTo(2);
  if(e.code==='KeyQ')cycleWeapon();
});
document.addEventListener('keyup',e=>{keys[e.code]=false;});
canvas.addEventListener('mousemove',e=>{if(!localPlayer)return;const r=canvas.getBoundingClientRect();localPlayer.aimAngle=Math.atan2(e.clientY-r.top-(localPlayer.y-cam.y),e.clientX-r.left-(localPlayer.x-cam.x));});
canvas.addEventListener('mousedown',e=>{if(e.button===0)shoot();});
function switchTo(idx){if(!localPlayer||idx>=localPlayer.weapons.length)return;localPlayer.weaponIdx=idx;shootCD=0;updWeaponBar();updHUD();}

// ── Game Loop ─────────────────────────────────────────
let lastT=0,animId=null,shootCD=0;

function startGame(seed){
  document.getElementById('lobbyScreen').style.display='none';
  document.getElementById('mainMenu').style.display='none';
  document.getElementById('hud').style.display='flex';
  document.getElementById('minimap').style.display='block';
  document.getElementById('connStatus').style.display='block';
  document.getElementById('joystickLeft').style.display='block';
  document.getElementById('joystickRight').style.display='block';
  document.getElementById('weaponBar').style.display='flex';
  document.getElementById('switchWepBtn').style.display='flex';
  gameState='playing';kills=0;lootCollected=0;gameTime=ZONE_CLOSE_TIME;shootCD=0;
  genMap(seed);
  localPlayer=mkLocal();players[myId]=localPlayer;
  spawnEnemies();spawnLoot();
  resize();initJoy();updHUD();updWeaponBar();
  gameTimer=setInterval(()=>{gameTime--;updZone();if(gameTime<=0)playerDead('ZONE');},1000);
  lastT=performance.now();
  if(animId)cancelAnimationFrame(animId);
  animId=requestAnimationFrame(loop);
}

function loop(now){
  const dt=Math.min((now-lastT)/1000,0.05);lastT=now;
  if(gameState==='playing'){update(dt);draw();}
  animId=requestAnimationFrame(loop);
}

function update(dt){
  if(!localPlayer||localPlayer.dead)return;
  shootCD-=dt*1000;
  // Movement
  let mx=0,my=0;
  if(keys['KeyW']||keys['ArrowUp'])my-=1;if(keys['KeyS']||keys['ArrowDown'])my+=1;
  if(keys['KeyA']||keys['ArrowLeft'])mx-=1;if(keys['KeyD']||keys['ArrowRight'])mx+=1;
  mx+=jL.dx;my+=jL.dy;
  const ml=Math.hypot(mx,my);if(ml>1){mx/=ml;my/=ml;}
  const spd=PLAYER_SPEED*dt,r=localPlayer.radius;
  const nx=localPlayer.x+mx*spd,ny=localPlayer.y+my*spd;
  if(!solid(nx+r,localPlayer.y)&&!solid(nx-r,localPlayer.y))localPlayer.x=nx;
  if(!solid(localPlayer.x,ny+r)&&!solid(localPlayer.x,ny-r))localPlayer.y=ny;
  // Right joystick = aim + shoot
  if(jR.active&&(Math.abs(jR.dx)>0.1||Math.abs(jR.dy)>0.1)){localPlayer.aimAngle=Math.atan2(jR.dy,jR.dx);shoot();}
  // Reload timer
  const w=curWep();
  if(w&&w.reloading&&performance.now()-w.reloadStart>=w.reloadTime){
    const need=WEAPONS[w.key].ammo-w.ammo,take=Math.min(need,w.reserve);
    w.ammo+=take;w.reserve-=take;w.reloading=false;updHUD();updWeaponBar();
  }
  updCam();updEnemies(dt);updBullets(dt);
  for(let i=particles.length-1;i>=0;i--){const p=particles[i];p.x+=p.vx*dt;p.y+=p.vy*dt;p.life-=dt;p.vx*=0.92;p.vy*=0.92;if(p.life<=0)particles.splice(i,1);}
  // Loot pickup
  for(const l of lootItems)if(!l.collected&&Math.hypot(l.x-localPlayer.x,l.y-localPlayer.y)<30)collectLoot(l);
  // Extraction
  const dex=Math.hypot(localPlayer.x-extractionZone.x,localPlayer.y-extractionZone.y);
  if(dex<extractionZone.r){
    if(!isExtracting){isExtracting=true;extractionProgress=0;}
    extractionProgress+=dt*1000;
    document.getElementById('extractionTimer').style.display='block';
    document.getElementById('extractionTimer').innerHTML=`⬆ EXTRACTING<br>${Math.ceil((EXTRACTION_TIME-extractionProgress)/1000)}s`;
    if(extractionProgress>=EXTRACTION_TIME)playerExtracted();
  }else{isExtracting=false;extractionProgress=0;document.getElementById('extractionTimer').style.display='none';}
  if(Object.keys(dataChannels).length>0)broadcast({type:'state',id:myId,name:myName,x:localPlayer.x,y:localPlayer.y,a:localPlayer.aimAngle,h:localPlayer.health,dead:localPlayer.dead});
  updHUD();
}

function shoot(){
  if(!localPlayer||localPlayer.dead)return;
  const w=curWep();if(!w||w.reloading)return;
  if(w.ammo<=0){reload();return;}
  if(shootCD>0)return;
  shootCD=w.fireRate;w.ammo--;
  // Burst (shotgun fires multiple pellets)
  for(let i=0;i<w.burstCount;i++){
    const angle=localPlayer.aimAngle+(Math.random()-0.5)*w.spread*2;
    spawnBullet(localPlayer.x,localPlayer.y,angle,myId,w.bulletSpd,w.color,w.damage,w.range);
    broadcast({type:'shoot',x:localPlayer.x,y:localPlayer.y,a:angle,id:myId,spd:w.bulletSpd,clr:w.color,dmg:w.damage,rng:w.range});
  }
  spawnPart(localPlayer.x+Math.cos(localPlayer.aimAngle)*16,localPlayer.y+Math.sin(localPlayer.aimAngle)*16,'#ffffaa',3);
  if(w.ammo<=0)reload();
  updWeaponBar();
}
function reload(){
  const w=curWep();if(!w||w.reloading||w.reserve<=0||w.ammo>=WEAPONS[w.key].ammo)return;
  w.reloading=true;w.reloadStart=performance.now();
}

function updEnemies(dt){
  const allP=Object.values(players).filter(p=>!p.dead);
  for(const en of enemies){
    if(en.health<=0)continue;
    en.alertCooldown=Math.max(0,(en.alertCooldown||0)-dt);
    // Find nearest player WITH line of sight
    let near=null,nd=Infinity,hasVis=false;
    for(const p of allP){
      const d=Math.hypot(p.x-en.x,p.y-en.y);
      if(d<nd){
        nd=d;near=p;
        hasVis=(d<=en.sight&&hasLOS(en.x,en.y,p.x,p.y));
      }
    }
    if(!near)continue;
    const angle=Math.atan2(near.y-en.y,near.x-en.x);
    if(hasVis){
      // Can see player → update last known position
      en.lastKnownX=near.x;en.lastKnownY=near.y;
      en.alertCooldown=3.5; // stay alert 3.5s after losing sight
      en.state='chase';
    }else if(en.alertCooldown>0){
      // Can't see but recently saw → go to last known pos
      en.state='search';
    }else{
      en.state='patrol';
    }
    let moveAngle=en.patrolAngle;
    if(en.state==='chase'||en.state==='search'){
      const tx=en.state==='chase'?near.x:en.lastKnownX;
      const ty=en.state==='chase'?near.y:en.lastKnownY;
      moveAngle=Math.atan2(ty-en.y,tx-en.x);
      if(en.state==='search'&&Math.hypot(en.x-en.lastKnownX,en.y-en.lastKnownY)<20){
        // Reached last known pos → patrol
        en.alertCooldown=0;
      }
    }else{
      en.patrolAngle+=dt*0.4;
    }
    const nx2=en.x+Math.cos(moveAngle)*en.speed*dt;
    const ny2=en.y+Math.sin(moveAngle)*en.speed*dt;
    if(!solid(nx2,ny2)){en.x=nx2;en.y=ny2;}
    else{en.patrolAngle+=0.3;} // bounce off wall
    // Shoot only if has direct line of sight
    if((isHost||Object.keys(peers).length===0)&&hasVis&&nd<en.sight*0.95){
      en.lastShot=(en.lastShot||0)+dt*1000;
      if(en.lastShot>en.fireRate){
        en.lastShot=0;
        const spread=en.type==='sniper'?0.02:0.12;
        spawnBullet(en.x,en.y,angle+(Math.random()-0.5)*spread,en.id,
          en.type==='sniper'?700:en.type==='heavy'?280:380,
          en.type==='sniper'?'#dd44ff':en.type==='heavy'?'#ff8844':'#ff4400',
          en.damage,1.6);
      }
    }
  }
}

function updBullets(dt){
  for(let i=bullets.length-1;i>=0;i--){
    const b=bullets[i];b.x+=b.vx*dt;b.y+=b.vy*dt;b.life-=dt;
    if(b.life<=0||solid(b.x,b.y)){spawnPart(b.x,b.y,b.color,4);bullets.splice(i,1);continue;}
    if(cover(b.x,b.y)){spawnPart(b.x,b.y,'#888',3);bullets.splice(i,1);continue;}
    if(b.ownerId===myId){
      let hit=false;
      for(let j=enemies.length-1;j>=0;j--){
        const en=enemies[j];if(en.health<=0)continue;
        if(Math.hypot(b.x-en.x,b.y-en.y)<en.radius+b.radius){
          en.health-=b.damage;spawnPart(en.x,en.y,en.color,8);bullets.splice(i,1);hit=true;
          if(en.health<=0){kills++;lootCollected+=en.reward;addKill(`${curWep()?.icon||''} ${en.type.toUpperCase()}`);spawnPart(en.x,en.y,en.color,20);
            lootItems.push({type:'valuables',icon:'💰',value:en.reward,color:'#ffd700',label:`+${en.reward} LOOT`,x:en.x,y:en.y,id:'el_'+en.id,collected:false});
            broadcast({type:'enemy_dead',id:en.id});}break;
        }
      }
      if(!hit)Object.values(players).forEach(p=>{if(p.isLocal||p.dead)return;if(Math.hypot(b.x-p.x,b.y-p.y)<p.radius+b.radius)broadcast({type:'hit',target:p.id,killer:myId,dmg:b.damage});});
    }
    if(b.ownerId.startsWith('e_')&&localPlayer&&!localPlayer.dead){
      if(Math.hypot(b.x-localPlayer.x,b.y-localPlayer.y)<localPlayer.radius+b.radius){
        localPlayer.health-=b.damage;showHit();spawnPart(localPlayer.x,localPlayer.y,'#ff2a2a',6);
        bullets.splice(i,1);broadcast({type:'hit',target:myId,killer:b.ownerId,dmg:b.damage});
        if(localPlayer.health<=0)playerDead('ENEMY');
      }
    }
  }
}

function collectLoot(l){
  l.collected=true;
  if(l.type==='ammo'){const w=curWep();if(w){w.reserve=Math.min(w.reserve+WEAPONS[w.key].reserve,WEAPONS[w.key].reserve*3);showPopup('🔋 AMMO +'+WEAPONS[w.key].reserve);}else showPopup('🔋 AMMO');
  }else if(l.type==='health'){localPlayer.health=Math.min(localPlayer.health+l.value,MAX_HEALTH);showPopup(l.label);
  }else if(l.type==='weapon'){equipWeaponLoot(l.wkey);
  }else{lootCollected+=l.value;showPopup(l.label);}
  spawnPart(l.x,l.y,l.color,12);broadcast({type:'loot_taken',lootId:l.id});
  updHUD();updWeaponBar();
}

let popT=null;
function showPopup(t){const el=document.getElementById('lootPopup');el.textContent=t;el.style.opacity='1';if(popT)clearTimeout(popT);popT=setTimeout(()=>el.style.opacity='0',1600);}
function showHit(){canvas.style.filter='brightness(2) saturate(0)';setTimeout(()=>canvas.style.filter='',80);}
let kfItems=[];
function addKill(t){const el=document.getElementById('killfeed'),e=document.createElement('div');e.className='kill-entry';e.textContent=t;el.prepend(e);kfItems.push(e);if(kfItems.length>5){kfItems[0].remove();kfItems.shift();}setTimeout(()=>{try{e.remove();}catch(_){}},4000);}

// ── Draw ──────────────────────────────────────────────
const TC={0:'#0a1520',1:'#1a2535',2:'#162030',3:'#061520',4:'#0a1f10'};

function draw(){
  resize();ctx.clearRect(0,0,canvas.width,canvas.height);
  ctx.save();ctx.translate(-cam.x,-cam.y);
  drawMap();drawEZ();drawLoot();drawEnemies();drawBullets();drawParticles();drawPlayers();
  ctx.restore();drawMM();
}
function resize(){const w=window.innerWidth,h=window.innerHeight;if(canvas.width!==w||canvas.height!==h){canvas.width=w;canvas.height=h;}}
window.addEventListener('resize',()=>{resize();updCam();});

function drawMap(){
  const sx=Math.max(0,Math.floor(cam.x/TILE)-1),sy=Math.max(0,Math.floor(cam.y/TILE)-1);
  const ex=Math.min(MAP_W,sx+Math.ceil(canvas.width/TILE)+2),ey=Math.min(MAP_H,sy+Math.ceil(canvas.height/TILE)+2);
  for(let ty=sy;ty<ey;ty++)for(let tx=sx;tx<ex;tx++){
    const t=tileMap[ty][tx],px=tx*TILE,py=ty*TILE;
    ctx.fillStyle=TC[t]||TC[0];ctx.fillRect(px,py,TILE,TILE);
    if(t===1){ctx.fillStyle='#233045';ctx.fillRect(px,py,TILE,6);ctx.strokeStyle='rgba(50,80,120,0.4)';ctx.lineWidth=1;ctx.strokeRect(px+.5,py+.5,TILE-1,TILE-1);}
    if(t===2){ctx.fillStyle='#1a3040';ctx.fillRect(px+4,py+4,TILE-8,TILE-8);ctx.strokeStyle='rgba(0,180,200,0.4)';ctx.lineWidth=1;ctx.strokeRect(px+4,py+4,TILE-8,TILE-8);ctx.fillStyle='#2a4560';ctx.fillRect(px+4,py+4,TILE-8,5);}
    if(t===4){const g=ctx.createLinearGradient(px,py,px+TILE,py+TILE);g.addColorStop(0,'rgba(0,80,30,0.5)');g.addColorStop(1,'rgba(0,200,80,0.1)');ctx.fillStyle=g;ctx.fillRect(px,py,TILE,TILE);}
    if(t===0||t===4){ctx.strokeStyle='rgba(0,100,150,0.06)';ctx.lineWidth=0.5;ctx.strokeRect(px,py,TILE,TILE);}
  }
}
function drawEZ(){
  const{x,y,r}=extractionZone,t=Date.now()*0.002;
  for(let i=0;i<3;i++){const ph=(t+i*0.7)%1;ctx.beginPath();ctx.arc(x,y,r*(0.7+ph*0.5),0,Math.PI*2);ctx.strokeStyle=`rgba(0,255,100,${0.5*(1-ph)})`;ctx.lineWidth=2;ctx.stroke();}
  ctx.beginPath();ctx.arc(x,y,r,0,Math.PI*2);ctx.strokeStyle='rgba(0,255,100,0.7)';ctx.lineWidth=2;ctx.stroke();
  ctx.fillStyle='rgba(0,255,100,0.8)';ctx.font='bold 11px monospace';ctx.textAlign='center';ctx.fillText('EXTRACTION',x,y-r-10);
  if(isExtracting&&extractionProgress>0){ctx.beginPath();ctx.arc(x,y,r+8,-Math.PI/2,-Math.PI/2+Math.PI*2*(extractionProgress/EXTRACTION_TIME));ctx.strokeStyle='#ffd700';ctx.lineWidth=4;ctx.stroke();}
}
function drawLoot(){
  for(const l of lootItems){
    if(l.collected)continue;
    const bob=Math.sin(Date.now()*0.003+l.x)*3;
    ctx.shadowColor=l.color;ctx.shadowBlur=12;
    ctx.beginPath();ctx.arc(l.x,l.y+bob,10,0,Math.PI*2);ctx.fillStyle=l.color+'44';ctx.fill();
    // Weapon loot gets a bigger glow
    if(l.type==='weapon'){ctx.beginPath();ctx.arc(l.x,l.y+bob,14,0,Math.PI*2);ctx.strokeStyle=l.color+'99';ctx.lineWidth=2;ctx.stroke();}
    ctx.shadowBlur=0;ctx.font='16px serif';ctx.textAlign='center';ctx.fillText(l.icon,l.x,l.y+bob+6);
  }
}
function drawEnemies(){
  for(const en of enemies){
    if(en.health<=0)continue;
    const{x,y,radius:r,color,health,maxHealth}=en;
    ctx.beginPath();ctx.ellipse(x,y+r*0.8,r*0.7,r*0.3,0,0,Math.PI*2);ctx.fillStyle='rgba(0,0,0,0.4)';ctx.fill();
    ctx.beginPath();ctx.arc(x,y,r,0,Math.PI*2);ctx.fillStyle=color;ctx.fill();
    ctx.strokeStyle='rgba(0,0,0,0.5)';ctx.lineWidth=2;ctx.stroke();
    // Alert indicator
    if(en.state==='chase'){ctx.beginPath();ctx.arc(x,y,r+5,0,Math.PI*2);ctx.strokeStyle='rgba(255,60,0,0.7)';ctx.lineWidth=1.5;ctx.stroke();}
    else if(en.state==='search'){ctx.beginPath();ctx.arc(x,y,r+5,0,Math.PI*2);ctx.strokeStyle='rgba(255,200,0,0.5)';ctx.lineWidth=1;ctx.setLineDash([3,3]);ctx.stroke();ctx.setLineDash([]);}
    // Eye
    if(localPlayer&&en.state!=='patrol'){const a=Math.atan2(localPlayer.y-y,localPlayer.x-x);ctx.beginPath();ctx.arc(x+Math.cos(a)*r*0.4,y+Math.sin(a)*r*0.4,3,0,Math.PI*2);ctx.fillStyle='#ff0';ctx.fill();}
    if(health<maxHealth){const bw=r*2;ctx.fillStyle='rgba(0,0,0,0.6)';ctx.fillRect(x-bw/2,y-r-9,bw,4);ctx.fillStyle=health>maxHealth/2?'#00ff88':'#ff2a2a';ctx.fillRect(x-bw/2,y-r-9,bw*(health/maxHealth),4);}
    // Type label
    ctx.font='8px monospace';ctx.textAlign='center';ctx.fillStyle='rgba(255,255,255,0.4)';ctx.fillText(en.type[0].toUpperCase(),x,y+r+10);
  }
}
function drawBullets(){
  for(const b of bullets){
    ctx.beginPath();ctx.arc(b.x,b.y,b.radius,0,Math.PI*2);ctx.fillStyle=b.color;ctx.shadowColor=b.color;ctx.shadowBlur=8;ctx.fill();ctx.shadowBlur=0;
    ctx.beginPath();ctx.moveTo(b.x,b.y);ctx.lineTo(b.x-b.vx*0.028,b.y-b.vy*0.028);ctx.strokeStyle=b.color+'88';ctx.lineWidth=2;ctx.stroke();
  }
}
function drawParticles(){
  for(const p of particles){const a=p.life/(p.maxLife||0.8);ctx.beginPath();ctx.arc(p.x,p.y,p.radius*a,0,Math.PI*2);ctx.fillStyle=p.color+Math.floor(a*255).toString(16).padStart(2,'0');ctx.fill();}
}
function drawPlayers(){Object.values(players).forEach(p=>drawPlayer(p));}
function drawPlayer(p){
  if(p.dead)return;
  const{x,y,radius:r,color,aimAngle,health,maxHealth,name,isLocal}=p;
  ctx.beginPath();ctx.ellipse(x,y+r*0.8,r*0.7,r*0.3,0,0,Math.PI*2);ctx.fillStyle='rgba(0,0,0,0.4)';ctx.fill();
  if(isLocal){ctx.shadowColor=color;ctx.shadowBlur=15;}
  ctx.beginPath();ctx.arc(x,y,r,0,Math.PI*2);ctx.fillStyle=isLocal?'#0a1520':'#1a0a00';ctx.fill();
  ctx.strokeStyle=color;ctx.lineWidth=isLocal?2.5:2;ctx.stroke();ctx.shadowBlur=0;
  const gl=r+14;ctx.beginPath();ctx.moveTo(x+Math.cos(aimAngle)*r,y+Math.sin(aimAngle)*r);ctx.lineTo(x+Math.cos(aimAngle)*gl,y+Math.sin(aimAngle)*gl);ctx.strokeStyle=color;ctx.lineWidth=3;ctx.stroke();
  // Show current weapon icon above player
  if(isLocal&&curWep()){ctx.font='12px serif';ctx.textAlign='center';ctx.fillText(curWep().icon,x,y-r-16);}
  ctx.font='10px monospace';ctx.fillStyle=isLocal?'rgba(0,255,231,0.8)':'rgba(255,100,50,0.8)';ctx.fillText(name,x,y-r-5);
  const bw=r*2.5;ctx.fillStyle='rgba(0,0,0,0.6)';ctx.fillRect(x-bw/2,y-r-3,bw,3);ctx.fillStyle=isLocal?(health>30?'#00ff88':'#ff2a2a'):'#ff6b35';ctx.fillRect(x-bw/2,y-r-3,bw*(health/maxHealth),3);
  if(isLocal){const w=curWep();if(w&&w.reloading){const pct=(performance.now()-w.reloadStart)/w.reloadTime;ctx.beginPath();ctx.arc(x,y,r+7,-Math.PI/2,-Math.PI/2+Math.PI*2*pct);ctx.strokeStyle='#ffd700';ctx.lineWidth=3;ctx.stroke();}}
}
function drawMM(){
  mmCtx.clearRect(0,0,110,110);mmCtx.fillStyle='rgba(0,5,10,0.85)';mmCtx.fillRect(0,0,110,110);
  const sx=110/(MAP_W*TILE),sy2=110/(MAP_H*TILE);
  for(let ty=0;ty<MAP_H;ty++)for(let tx=0;tx<MAP_W;tx++){const t=tileMap[ty][tx];if(t===1){mmCtx.fillStyle='#2a3a50';mmCtx.fillRect(tx*TILE*sx,ty*TILE*sy2,TILE*sx+.5,TILE*sy2+.5);}if(t===4){mmCtx.fillStyle='rgba(0,200,80,0.4)';mmCtx.fillRect(tx*TILE*sx,ty*TILE*sy2,TILE*sx+.5,TILE*sy2+.5);}}
  for(const en of enemies){if(en.health<=0)continue;mmCtx.beginPath();mmCtx.arc(en.x*sx,en.y*sy2,1.5,0,Math.PI*2);mmCtx.fillStyle=en.state==='chase'?'#ff4400':'#ff2a2a';mmCtx.fill();}
  for(const l of lootItems){if(l.collected)continue;mmCtx.beginPath();mmCtx.arc(l.x*sx,l.y*sy2,l.type==='weapon'?2.5:1,0,Math.PI*2);mmCtx.fillStyle=l.type==='weapon'?l.color:'#ffd700';mmCtx.fill();}
  Object.values(players).filter(p=>!p.isLocal&&!p.dead).forEach(p=>{mmCtx.beginPath();mmCtx.arc(p.x*sx,p.y*sy2,2.5,0,Math.PI*2);mmCtx.fillStyle='#ff6b35';mmCtx.fill();});
  if(localPlayer){mmCtx.beginPath();mmCtx.arc(localPlayer.x*sx,localPlayer.y*sy2,3,0,Math.PI*2);mmCtx.fillStyle='#00ffe7';mmCtx.fill();}
  mmCtx.strokeStyle='rgba(0,255,231,0.3)';mmCtx.lineWidth=0.5;mmCtx.strokeRect(cam.x*sx,cam.y*sy2,canvas.width*sx,canvas.height*sy2);
}

// ── HUD ───────────────────────────────────────────────
function updHUD(){
  if(!localPlayer)return;
  document.getElementById('healthVal').textContent=Math.max(0,Math.floor(localPlayer.health));
  document.getElementById('healthFill').style.width=Math.max(0,localPlayer.health/MAX_HEALTH*100)+'%';
  document.getElementById('lootVal').textContent=lootCollected;
  document.getElementById('killCount').textContent=kills;
  const w=curWep();document.getElementById('ammoDisplay').textContent=w?`${w.ammo}/${w.reserve}${w.reloading?' ↺':''}`:'-';
}
function updZone(){const m=Math.floor(gameTime/60).toString().padStart(2,'0'),s=(gameTime%60).toString().padStart(2,'0');document.getElementById('zoneTimer').textContent=`${m}:${s}`;if(gameTime<=30)document.getElementById('zoneTimer').style.color='#ff2a2a';}
function setConn(c,t){document.getElementById('connDot').className='dot '+c;document.getElementById('connText').textContent=t;}

// ── End States ────────────────────────────────────────
function playerDead(killer){
  if(gameState!=='playing')return;gameState='dead';localPlayer.dead=true;clearInterval(gameTimer);saveRun(false);
  document.getElementById('gameOverScreen').style.display='flex';
  document.getElementById('goTitle').textContent='ELIMINATED';document.getElementById('goTitle').className='go-title dead';
  document.getElementById('goStats').textContent=`KILLS: ${kills}  ·  LOOT: ${lootCollected}  ·  BY: ${killer}`;
  ['hud','minimap','joystickLeft','joystickRight','weaponBar','switchWepBtn'].forEach(id=>document.getElementById(id).style.display='none');
}
function playerExtracted(){
  if(gameState!=='playing')return;gameState='won';clearInterval(gameTimer);saveRun(true);
  document.getElementById('gameOverScreen').style.display='flex';
  document.getElementById('goTitle').textContent='EXTRACTED';document.getElementById('goTitle').className='go-title win';
  document.getElementById('goStats').textContent=`KILLS: ${kills}  ·  LOOT: ${lootCollected}  ·  TIME: ${ZONE_CLOSE_TIME-gameTime}s`;
  ['hud','minimap','joystickLeft','joystickRight','weaponBar','switchWepBtn'].forEach(id=>document.getElementById(id).style.display='none');
}
async function saveRun(ok){
  dbSet('runs',{date:new Date().toISOString(),success:ok,kills,loot:lootCollected,timeSurvived:ZONE_CLOSE_TIME-gameTime});
  let pr=await dbGet('profile','main')||{id:'main',totalRuns:0,totalKills:0,totalLoot:0,extractions:0};
  pr.totalRuns++;pr.totalKills+=kills;pr.totalLoot+=lootCollected;if(ok)pr.extractions++;dbSet('profile',pr);
}

// ── PWA ───────────────────────────────────────────────
function setupPWA(){
  function mkIcon(size){const c=document.createElement('canvas');c.width=c.height=size;const x=c.getContext('2d');x.fillStyle='#040a0f';x.fillRect(0,0,size,size);x.beginPath();x.arc(size/2,size/2,size*0.44,0,Math.PI*2);x.strokeStyle='#00ffe7';x.lineWidth=size*0.04;x.stroke();x.strokeStyle='#00ffe7';x.lineWidth=size*0.06;x.beginPath();x.moveTo(size/2,size*0.25);x.lineTo(size/2,size*0.75);x.stroke();x.beginPath();x.moveTo(size*0.25,size/2);x.lineTo(size*0.75,size/2);x.stroke();x.fillStyle='#ffd700';[0,1,2,3].forEach(i=>{const a=i*Math.PI/2+Math.PI/4;x.beginPath();x.arc(size/2+Math.cos(a)*size*0.32,size/2+Math.sin(a)*size*0.32,size*0.06,0,Math.PI*2);x.fill();});x.fillStyle='#00ffe7';x.font=`bold ${size*0.13}px monospace`;x.textAlign='center';x.textBaseline='middle';x.fillText('EXFIL',size/2,size*0.5);return c.toDataURL('image/png');}
  const i192=mkIcon(192),i512=mkIcon(512);
  const manifest={name:'EXFIL // ZERO',short_name:'EXFIL',description:'Top-Down Extraction Shooter',start_url:'./'+location.search,display:'fullscreen',orientation:'landscape',background_color:'#040a0f',theme_color:'#040a0f',icons:[{src:i192,sizes:'192x192',type:'image/png',purpose:'any maskable'},{src:i512,sizes:'512x512',type:'image/png',purpose:'any maskable'}]};
  const blob=new Blob([JSON.stringify(manifest)],{type:'application/manifest+json'});
  const link=document.createElement('link');link.rel='manifest';link.href=URL.createObjectURL(blob);document.head.appendChild(link);
  const al=document.createElement('link');al.rel='apple-touch-icon';al.href=i192;document.head.appendChild(al);
  const isIOS=/iphone|ipad|ipod/i.test(navigator.userAgent);
  const isSA=window.navigator.standalone===true||window.matchMedia('(display-mode:fullscreen)').matches;
  if(isIOS&&!isSA&&!sessionStorage.getItem('iosHint')){sessionStorage.setItem('iosHint','1');setTimeout(()=>{const h=document.createElement('div');h.style.cssText='position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:rgba(0,20,30,0.96);border:1px solid #00ffe7;color:#00ffe7;font-family:monospace;font-size:0.72rem;line-height:1.6;padding:12px 18px;z-index:9999;text-align:center;max-width:280px;box-shadow:0 0 20px rgba(0,255,231,0.3);';h.innerHTML='📲 Safari → Teilen → Zum Home-Bildschirm<br><small style="opacity:0.5">Tippen zum Schließen</small>';h.onclick=()=>h.remove();document.body.appendChild(h);setTimeout(()=>{if(h.parentNode)h.remove();},8000);},2500);}
  window.addEventListener('beforeinstallprompt',e=>{e.preventDefault();const b=document.createElement('div');b.style.cssText='position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:rgba(0,20,30,0.96);border:1px solid #ffd700;color:#ffd700;font-family:monospace;font-size:0.8rem;padding:10px 20px;z-index:9999;cursor:pointer;letter-spacing:0.15em;white-space:nowrap;box-shadow:0 0 20px rgba(255,215,0,0.3);';b.textContent='⬇ AUF HOME-SCREEN INSTALLIEREN';b.onclick=()=>{e.prompt();b.remove();};document.body.appendChild(b);setTimeout(()=>{if(b.parentNode)b.remove();},8000);});
}

window.addEventListener('load',()=>{resize();initDB();setupPWA();});
document.addEventListener('contextmenu',e=>e.preventDefault());
</script>
</body>
</html>
