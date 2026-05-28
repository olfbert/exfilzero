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
:root{--neon:#00ffe7;--red:#ff2a2a;--gold:#ffd700;--dark:#040a0f;--panel:rgba(0,20,30,0.95);--border:rgba(0,255,231,0.3);}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent;-webkit-touch-callout:none;}
html,body{width:100%;height:100%;overflow:hidden;background:#040a0f;touch-action:none;user-select:none;-webkit-user-select:none;}
body{font-family:'Share Tech Mono',monospace;color:var(--neon);padding:env(safe-area-inset-top,0px) env(safe-area-inset-right,0px) env(safe-area-inset-bottom,0px) env(safe-area-inset-left,0px);}
#gameCanvas{position:fixed;top:0;left:0;display:block;}
.screen{position:fixed;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:20px 16px;padding-top:max(20px,env(safe-area-inset-top));background:radial-gradient(ellipse at center,#001a24 0%,#040a0f 70%);z-index:100;overflow-y:auto;}
.screen.centered{justify-content:center;}
.logo{font-family:'Orbitron',sans-serif;font-weight:900;font-size:clamp(1.6rem,6vw,3.5rem);letter-spacing:0.2em;color:var(--neon);text-shadow:0 0 20px var(--neon),0 0 60px rgba(0,255,231,0.4);animation:pulse 2s ease-in-out infinite;text-align:center;}
.tagline{font-size:0.75rem;letter-spacing:0.4em;color:rgba(0,255,231,0.5);margin:0.3rem 0 1.5rem;text-align:center;}
@keyframes pulse{0%,100%{text-shadow:0 0 20px var(--neon),0 0 60px rgba(0,255,231,0.4);}50%{text-shadow:0 0 40px var(--neon),0 0 100px rgba(0,255,231,0.6),0 0 3px #fff;}}
.btn{background:transparent;border:1px solid var(--neon);color:var(--neon);font-family:'Share Tech Mono',monospace;font-size:0.85rem;letter-spacing:0.15em;padding:0.65rem 1.6rem;cursor:pointer;margin:0.25rem;transition:background 0.15s,color 0.15s;text-transform:uppercase;min-width:190px;position:relative;overflow:hidden;}
.btn:active{background:var(--neon);color:var(--dark);}
.btn.danger{border-color:var(--red);color:var(--red);}
.btn.danger:active{background:var(--red);color:#fff;}
.btn.gold{border-color:var(--gold);color:var(--gold);}
.btn.gold:active{background:var(--gold);color:var(--dark);}
.btn.sm{min-width:0;font-size:0.75rem;padding:0.5rem 1rem;}
input[type=text]{background:rgba(0,255,231,0.05);border:1px solid var(--border);color:var(--neon);font-family:'Share Tech Mono',monospace;font-size:0.85rem;padding:0.55rem 0.9rem;width:220px;letter-spacing:0.1em;margin:0.25rem;outline:none;text-align:center;}
input:focus{border-color:var(--neon);}
input::placeholder{color:rgba(0,255,231,0.3);}
/* HUD */
#hud{position:fixed;top:0;left:0;right:0;padding:8px 10px;display:none;align-items:flex-start;justify-content:space-between;pointer-events:none;z-index:10;}
.hp{background:var(--panel);border:1px solid var(--border);padding:5px 9px;min-width:100px;}
.hl{font-size:0.5rem;letter-spacing:0.3em;color:rgba(0,255,231,0.5);margin-bottom:1px;}
.hv{font-family:'Orbitron',sans-serif;font-size:0.85rem;font-weight:700;}
#hBar{width:100%;height:3px;background:rgba(255,42,42,0.2);margin-top:3px;}
#hFill{height:100%;background:var(--red);transition:width 0.2s;box-shadow:0 0 5px var(--red);}
#armorBar{width:100%;height:2px;background:rgba(0,150,255,0.15);margin-top:2px;}
#armorFill{height:100%;background:#4af;transition:width 0.2s;}
/* Weapon bar */
#wBar{position:fixed;bottom:calc(148px + env(safe-area-inset-bottom,0px));left:50%;transform:translateX(-50%);display:none;gap:5px;z-index:20;pointer-events:all;}
.ws{width:50px;height:50px;border:1px solid var(--border);background:var(--panel);display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:0.55rem;color:rgba(0,255,231,0.5);position:relative;cursor:pointer;}
.ws.active{border-color:var(--neon);box-shadow:0 0 10px rgba(0,255,231,0.4);}
.ws .wi{font-size:1rem;margin-bottom:1px;}
.ws .wk{position:absolute;top:2px;left:3px;font-size:0.45rem;color:var(--gold);}
/* Swap btn */
#swapBtn{position:fixed;bottom:calc(25px + env(safe-area-inset-bottom,0px));left:50%;transform:translateX(-50%);width:58px;height:58px;border-radius:50%;background:rgba(255,215,0,0.12);border:2px solid var(--gold);color:var(--gold);font-size:0.55rem;z-index:51;display:none;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;}
#swapBtn:active{background:rgba(255,215,0,0.35);}
/* Joysticks */
#joyL,#joyR{position:fixed;bottom:calc(22px + env(safe-area-inset-bottom,0px));width:108px;height:108px;border-radius:50%;background:rgba(0,255,231,0.06);border:2px solid rgba(0,255,231,0.22);z-index:50;touch-action:none;display:none;}
#joyL{left:22px;}#joyR{right:22px;}
.jt{position:absolute;width:42px;height:42px;border-radius:50%;background:radial-gradient(circle,rgba(0,255,231,0.7),rgba(0,255,231,0.2));border:2px solid var(--neon);top:50%;left:50%;transform:translate(-50%,-50%);box-shadow:0 0 10px rgba(0,255,231,0.4);}
/* Misc HUD */
#lootPop{position:fixed;bottom:calc(215px + env(safe-area-inset-bottom,0px));left:50%;transform:translateX(-50%);background:var(--panel);border:1px solid var(--gold);color:var(--gold);padding:5px 14px;font-size:0.75rem;z-index:22;opacity:0;transition:opacity 0.3s;pointer-events:none;white-space:nowrap;}
#minimap{position:fixed;top:8px;right:8px;width:108px;height:108px;border:1px solid var(--border);background:rgba(0,10,15,0.85);z-index:10;display:none;}
#exTimer{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);font-family:'Orbitron',sans-serif;font-size:1.5rem;color:var(--gold);text-shadow:0 0 20px var(--gold);z-index:30;display:none;text-align:center;}
#killfeed{position:fixed;right:126px;top:8px;z-index:20;pointer-events:none;}
.kf{background:rgba(255,42,42,0.15);border-left:2px solid var(--red);padding:2px 7px;font-size:0.68rem;margin-bottom:2px;animation:fi 0.2s;}
@keyframes fi{from{opacity:0;transform:translateX(16px)}to{opacity:1}}
#connSt{position:fixed;bottom:calc(148px + env(safe-area-inset-bottom,0px));right:10px;font-size:0.6rem;color:rgba(0,255,231,0.45);z-index:20;display:none;}
.dot{display:inline-block;width:5px;height:5px;border-radius:50%;background:#888;margin-right:3px;}
.dot.g{background:#0f8;box-shadow:0 0 5px #0f8;}.dot.y{background:var(--gold);}.dot.r{background:var(--red);}
/* Lobby */
.lcode{font-family:'Orbitron',sans-serif;font-size:1.5rem;color:var(--gold);letter-spacing:0.4em;padding:0.6rem 1.4rem;border:1px solid var(--gold);margin:0.6rem 0;text-shadow:0 0 12px var(--gold);}
.li{font-size:0.72rem;color:rgba(0,255,231,0.6);margin:0.25rem 0;letter-spacing:0.12em;}
.pe{padding:3px 14px;border-left:2px solid var(--neon);margin:3px 0;font-size:0.82rem;}
/* Game Over */
.got{font-family:'Orbitron',sans-serif;font-size:clamp(1.6rem,6vw,2.8rem);font-weight:900;margin-bottom:0.6rem;text-align:center;}
.got.dead{color:var(--red);text-shadow:0 0 28px var(--red);}
.got.win{color:var(--gold);text-shadow:0 0 28px var(--gold);}
/* SHOP */
#shopScreen{display:none;}
.shop-title{font-family:'Orbitron',sans-serif;font-size:1.2rem;color:var(--gold);letter-spacing:0.3em;margin:0.5rem 0;text-align:center;}
.shop-currency{font-size:0.85rem;color:var(--gold);margin-bottom:0.8rem;text-align:center;letter-spacing:0.1em;}
.shop-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;width:100%;max-width:420px;margin-bottom:12px;}
.shop-item{background:var(--panel);border:1px solid var(--border);padding:10px 8px;text-align:center;position:relative;cursor:pointer;}
.shop-item.maxed{border-color:rgba(0,255,231,0.15);opacity:0.6;}
.shop-item .si-icon{font-size:1.6rem;margin-bottom:3px;}
.shop-item .si-name{font-size:0.65rem;letter-spacing:0.1em;color:var(--neon);margin-bottom:2px;}
.shop-item .si-desc{font-size:0.55rem;color:rgba(0,255,231,0.45);margin-bottom:4px;line-height:1.3;}
.shop-item .si-cost{font-size:0.7rem;color:var(--gold);}
.shop-item .si-lvl{position:absolute;top:4px;right:5px;font-size:0.55rem;color:rgba(255,215,0,0.6);}
.shop-item:active:not(.maxed){background:rgba(0,255,231,0.1);}
.scanline{position:fixed;inset:0;background:repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(0,0,0,0.07) 2px,rgba(0,0,0,0.07) 4px);pointer-events:none;z-index:999;}
</style>
</head>
<body>
<div class="scanline"></div>
<canvas id="gameCanvas"></canvas>

<!-- MAIN MENU -->
<div id="mainMenu" class="screen centered">
  <div class="logo">EXFIL // ZERO</div>
  <div class="tagline">Extract or Die Trying</div>
  <input type="text" id="nameInput" placeholder="CALL SIGN" maxlength="12">
  <button class="btn gold" onclick="startSolo()">SOLO RUN</button>
  <button class="btn" onclick="openShop()">UPGRADE SHOP</button>
  <button class="btn" onclick="createLobby()">HOST GAME</button>
  <button class="btn" onclick="showJoinSection()">JOIN GAME</button>
  <div id="joinSection" style="display:none;flex-direction:column;align-items:center;">
    <input type="text" id="codeInput" placeholder="ROOM CODE" maxlength="6" style="text-transform:uppercase;margin-top:4px;">
    <button class="btn" onclick="joinLobby()">CONNECT</button>
  </div>
  <div id="menuStats" style="margin-top:1rem;font-size:0.7rem;color:rgba(0,255,231,0.4);text-align:center;letter-spacing:0.1em;"></div>
</div>

<!-- SHOP -->
<div id="shopScreen" class="screen">
  <div class="logo" style="font-size:1.4rem;margin-top:0.5rem;">UPGRADE SHOP</div>
  <div class="shop-currency">💰 STASH: <span id="shopGold">0</span>  &nbsp;|&nbsp;  SPENT: <span id="shopSpent">0</span></div>
  <div class="shop-grid" id="shopGrid"></div>
  <button class="btn danger sm" onclick="closeShop()">BACK TO MENU</button>
</div>

<!-- LOBBY -->
<div id="lobbyScreen" class="screen centered" style="display:none">
  <div class="logo" style="font-size:1.5rem">STAGING AREA</div>
  <div class="li">SHARE THIS CODE</div>
  <div class="lcode" id="lobbyCode">------</div>
  <div class="li" id="lobbyStatus">WAITING...</div>
  <div id="playerList"></div>
  <button class="btn gold" id="startBtn" style="display:none;margin-top:8px;" onclick="hostStart()">DEPLOY</button>
  <button class="btn danger" onclick="leaveLobby()">ABORT</button>
</div>

<!-- HUD -->
<div id="hud">
  <div class="hp">
    <div class="hl">HEALTH</div>
    <div class="hv" id="hpVal" style="color:var(--red)">100</div>
    <div id="hBar"><div id="hFill" style="width:100%"></div></div>
    <div id="armorBar"><div id="armorFill" style="width:0%"></div></div>
  </div>
  <div class="hp" style="text-align:center">
    <div class="hl">LOOT</div>
    <div class="hv" style="color:var(--gold)" id="lootVal">0</div>
    <div class="hl" style="margin-top:2px">ZONE</div>
    <div class="hv" id="zoneT" style="font-size:0.78rem">--:--</div>
  </div>
  <div class="hp" style="text-align:right">
    <div class="hl">AMMO</div>
    <div class="hv" id="ammoD" style="color:var(--gold)">-/-</div>
    <div class="hl" style="margin-top:2px">KILLS</div>
    <div class="hv" id="killC">0</div>
  </div>
</div>

<div id="wBar"></div>
<canvas id="minimap" width="108" height="108"></canvas>
<div id="killfeed"></div>
<div id="connSt"><span class="dot" id="connDot"></span><span id="connTxt">OFFLINE</span></div>
<div id="lootPop"></div>
<div id="exTimer"></div>
<div id="joyL"><div class="jt" id="tL"></div></div>
<div id="joyR"><div class="jt" id="tR"></div></div>
<div id="swapBtn" onclick="cycleWep()"><span style="font-size:1.1rem">🔄</span>SWAP</div>

<!-- GAME OVER -->
<div id="goScreen" class="screen centered" style="display:none">
  <div class="got" id="goTitle">ELIMINATED</div>
  <div style="font-size:0.82rem;letter-spacing:0.12em;margin:0.6rem 0;color:rgba(0,255,231,0.7);text-align:center" id="goStats"></div>
  <div style="font-size:0.7rem;color:rgba(255,215,0,0.6);margin-bottom:1rem;text-align:center" id="goStash"></div>
  <button class="btn gold" id="goShopBtn" onclick="goToShop()">VISIT SHOP</button>
  <button class="btn" id="goMenuBtn" onclick="goToMenu()">MAIN MENU</button>
</div>

<script>
// ════════════════════════════════════════════════════
// EXFIL // ZERO  v4
// ════════════════════════════════════════════════════
const canvas=document.getElementById('gameCanvas');
const ctx=canvas.getContext('2d');
const mmCv=document.getElementById('minimap');
const mmCtx=mmCv.getContext('2d');

const TILE=40,MAP_W=64,MAP_H=64;
const BASE_SPEED=180, BASE_HEALTH=100;
const EXTRACTION_TIME=5000, ZONE_TIME=120;

// ── Weapon templates ──────────────────────────────
const WDEFS={
  pistol: {name:'PISTOL', icon:'🔫',ammo:12,res:48, fr:250,rt:1200,dmg:18,spd:480,spread:0.04,burst:1,col:'#fff',  rng:1.4},
  smg:    {name:'SMG',    icon:'⚡', ammo:30,res:120,fr:90, rt:1600,dmg:12,spd:520,spread:0.10,burst:1,col:'#fd4',  rng:1.2},
  shotgun:{name:'SHTGN',  icon:'💥',ammo:6, res:24, fr:700,rt:2000,dmg:14,spd:380,spread:0.30,burst:6,col:'#f84',  rng:0.7},
  rifle:  {name:'RIFLE',  icon:'🎯',ammo:20,res:60, fr:180,rt:2200,dmg:28,spd:680,spread:0.02,burst:1,col:'#4fb',  rng:2.0},
  sniper: {name:'SNIPER', icon:'🔭',ammo:5, res:15, fr:900,rt:2500,dmg:80,spd:900,spread:0.005,burst:1,col:'#a4f',rng:3.0},
};
function mkWep(key){const d=WDEFS[key];return{key,name:d.name,icon:d.icon,ammo:d.ammo,reserve:d.res,fireRate:d.fr,reloadTime:d.rt,damage:d.dmg,bulletSpd:d.spd,spread:d.spread,burst:d.burst,color:d.col,range:d.rng,reloading:false,reloadStart:0};}

// ── Upgrade shop definitions ───────────────────────
const UPGRADES=[
  {id:'maxhp',    icon:'❤️', name:'MAX HEALTH',   desc:'+20 HP per level',        maxLvl:5,  costs:[150,250,400,600,900]},
  {id:'speed',    icon:'👟', name:'MOVE SPEED',   desc:'+8% speed per level',     maxLvl:4,  costs:[200,350,550,800]},
  {id:'armor',    icon:'🛡️', name:'ARMOR',         desc:'+1 armor plate (20HP)',   maxLvl:3,  costs:[300,500,750]},
  {id:'reloads',  icon:'⚙️', name:'FAST RELOAD',  desc:'-15% reload time',        maxLvl:3,  costs:[180,300,480]},
  {id:'damage',   icon:'🔥', name:'DAMAGE BOOST', desc:'+10% damage per level',   maxLvl:4,  costs:[250,400,600,900]},
  {id:'sight',    icon:'👁️', name:'RADAR',         desc:'Enemies on minimap',      maxLvl:1,  costs:[350]},
  {id:'startgear',icon:'🎒', name:'STARTER GEAR', desc:'Start with SMG+50ammo',   maxLvl:1,  costs:[500]},
  {id:'extralife',icon:'💎', name:'INSURANCE',    desc:'Keep 30% loot on death',  maxLvl:1,  costs:[800]},
];

// ── IndexedDB ─────────────────────────────────────
let db;
async function initDB(){
  return new Promise(res=>{
    const r=indexedDB.open('ExfilZeroV4',1);
    r.onupgradeneeded=e=>{
      const d=e.target.result;
      if(!d.objectStoreNames.contains('profile'))d.createObjectStore('profile',{keyPath:'id'});
    };
    r.onsuccess=e=>{db=e.target.result;res();};
    r.onerror=()=>res();
  });
}
function dbSet(v){if(!db)return;db.transaction('profile','readwrite').objectStore('profile').put(v);}
function dbGet(k){return new Promise(res=>{if(!db)return res(null);const r=db.transaction('profile','readonly').objectStore('profile').get(k);r.onsuccess=()=>res(r.result);r.onerror=()=>res(null);});}

// ── Profile (persistent) ──────────────────────────
let profile={
  id:'main',
  stash:0,         // currency for shop
  totalSpent:0,
  upgrades:{},     // {id: level}
  totalRuns:0,totalKills:0,totalExtractions:0,totalLoot:0
};

async function loadProfile(){
  const p=await dbGet('main');
  if(p)profile=p;
  updMenuStats();
}
function saveProfile(){dbSet({...profile});}

function getUpgLvl(id){return profile.upgrades[id]||0;}

function calcMaxHP(){return BASE_HEALTH+getUpgLvl('maxhp')*20;}
function calcSpeed(){return BASE_SPEED*(1+getUpgLvl('speed')*0.08);}
function calcArmor(){return getUpgLvl('armor')*20;}
function calcDmgMult(){return 1+getUpgLvl('damage')*0.10;}
function calcReloadMult(){return 1-getUpgLvl('reloads')*0.15;}
function hasRadar(){return getUpgLvl('sight')>0;}
function hasInsurance(){return getUpgLvl('extralife')>0;}
function hasStarterGear(){return getUpgLvl('startgear')>0;}

// ── State ─────────────────────────────────────────
let gameState='menu';
let localPlayer=null,players={},enemies=[],bullets=[],lootItems=[],particles=[];
let tileMap=[],extractionZone={x:0,y:0,r:90};
let gameTime=ZONE_TIME,gameTimer=null;
let myId='p_'+Math.random().toString(36).slice(2,8);
let myName='GHOST',kills=0,sessionLoot=0;
let extractionProgress=0,isExtracting=false;
let animId=null,lastT=0,shootCD=0;

// ── P2P ───────────────────────────────────────────
let isHost=false,roomCode='';
let peers={},dataChannels={},signalingWs=null,pendingCandidates={};
const SIGNAL_URL='wss://socketsbay.com/wss/v2/1/demo/';
function connectSignaling(){try{signalingWs=new WebSocket(SIGNAL_URL);signalingWs.onopen=()=>setConn('y','SIGNAL');signalingWs.onmessage=e=>{try{onSignal(JSON.parse(e.data));}catch(_){}};signalingWs.onerror=()=>setConn('r','ERR');signalingWs.onclose=()=>{if(gameState==='playing')setConn('r','LOST');};}catch(_){setConn('r','NO NET');}}
function sig(msg){if(signalingWs&&signalingWs.readyState===1)signalingWs.send(JSON.stringify({room:roomCode,...msg}));}
async function onSignal(msg){
  if(msg.room!==roomCode||msg.from===myId)return;
  if(msg.type==='join'){if(isHost)await mkPC(msg.from,true);updLobby(msg.from,msg.name);}
  if(msg.type==='offer'){await mkPC(msg.from,false);await peers[msg.from].setRemoteDescription(new RTCSessionDescription(msg.sdp));const a=await peers[msg.from].createAnswer();await peers[msg.from].setLocalDescription(a);sig({type:'answer',from:myId,to:msg.from,sdp:a});flushC(msg.from);}
  if(msg.type==='answer'&&peers[msg.from]){await peers[msg.from].setRemoteDescription(new RTCSessionDescription(msg.sdp));flushC(msg.from);}
  if(msg.type==='ice'&&peers[msg.from]){try{await peers[msg.from].addIceCandidate(new RTCIceCandidate(msg.candidate));}catch(_){}}
  if(msg.type==='start'&&!isHost)startGame(msg.seed);
}
async function mkPC(pid,init){
  if(peers[pid])return;
  const pc=new RTCPeerConnection({iceServers:[{urls:'stun:stun.l.google.com:19302'}]});
  peers[pid]=pc;pendingCandidates[pid]=[];
  pc.onicecandidate=e=>{if(e.candidate)sig({type:'ice',from:myId,to:pid,candidate:e.candidate});};
  pc.onconnectionstatechange=()=>{if(pc.connectionState==='connected')setConn('g','P2P OK');if(pc.connectionState==='disconnected')onDrop(pid);};
  if(init){const dc=pc.createDataChannel('g',{ordered:false,maxRetransmits:0});setupDC(dc,pid);const o=await pc.createOffer();await pc.setLocalDescription(o);sig({type:'offer',from:myId,to:pid,sdp:o});}
  else pc.ondatachannel=e=>setupDC(e.channel,pid);
}
function flushC(pid){(pendingCandidates[pid]||[]).forEach(c=>{try{peers[pid].addIceCandidate(new RTCIceCandidate(c));}catch(_){}});pendingCandidates[pid]=[];}
function setupDC(dc,pid){dataChannels[pid]=dc;dc.onopen=()=>{setConn('g','OK');sendP(pid,{type:'hello',id:myId,name:myName});};dc.onmessage=e=>{try{onPeerMsg(pid,JSON.parse(e.data));}catch(_){}};dc.onerror=_=>{};}
function sendP(pid,msg){const dc=dataChannels[pid];if(dc&&dc.readyState==='open')try{dc.send(JSON.stringify(msg));}catch(_){}}
function broadcast(msg){Object.keys(dataChannels).forEach(id=>sendP(id,msg));}
function onPeerMsg(pid,msg){
  if(msg.type==='hello')players[msg.id]=players[msg.id]||mkRemote(msg.id,msg.name);
  if(msg.type==='state'){if(!players[msg.id])players[msg.id]=mkRemote(msg.id,msg.name||'?');const p=players[msg.id];p.x=msg.x;p.y=msg.y;p.aimAngle=msg.a;p.health=msg.h;p.dead=msg.dead;}
  if(msg.type==='shoot')spawnBullet(msg.x,msg.y,msg.a,msg.id,msg.spd,msg.col,msg.dmg,msg.rng);
  if(msg.type==='hit'&&msg.target===myId&&localPlayer){localPlayer.health-=msg.dmg;showHit();if(localPlayer.health<=0)playerDead(msg.killer);}
  if(msg.type==='loot_taken')lootItems=lootItems.filter(l=>l.id!==msg.lootId);
  if(msg.type==='enemy_dead'){const en=enemies.find(e=>e.id===msg.id);if(en)en.health=0;}
}
function onDrop(pid){if(players[pid])players[pid].dead=true;setConn('y','LEFT');}

// ── Lobby ─────────────────────────────────────────
let lobbyP={};
function updLobby(id,name){lobbyP[id]=name;renderLobby();}
function renderLobby(){
  document.getElementById('playerList').innerHTML=Object.entries(lobbyP).map(([id,n])=>`<div class="pe">▶ ${n}${id===myId?' (YOU)':''}</div>`).join('');
  const c=Object.keys(lobbyP).length;
  document.getElementById('lobbyStatus').textContent=`${c} OPERATOR${c!==1?'S':''} READY`;
  if(isHost&&c>=1)document.getElementById('startBtn').style.display='';
}
function showJoinSection(){const s=document.getElementById('joinSection');s.style.display=s.style.display==='none'?'flex':'none';}
async function createLobby(){
  myName=document.getElementById('nameInput').value.trim().toUpperCase()||'GHOST';
  isHost=true;roomCode=Math.random().toString(36).slice(2,8).toUpperCase();
  lobbyP={};updLobby(myId,myName);
  show('lobbyScreen');hide('mainMenu');
  document.getElementById('lobbyCode').textContent=roomCode;
  connectSignaling();setTimeout(()=>sig({type:'join',from:myId,name:myName}),800);
}
function joinLobby(){
  myName=document.getElementById('nameInput').value.trim().toUpperCase()||'SHADOW';
  roomCode=document.getElementById('codeInput').value.trim().toUpperCase();if(!roomCode)return;
  isHost=false;lobbyP={};updLobby(myId,myName);
  show('lobbyScreen');hide('mainMenu');
  document.getElementById('lobbyCode').textContent=roomCode;
  connectSignaling();setTimeout(()=>sig({type:'join',from:myId,name:myName}),800);
}
function leaveLobby(){if(signalingWs)try{signalingWs.close();}catch(_){}goToMenu();}
function hostStart(){const seed=Date.now();sig({type:'start',from:myId,seed});startGame(seed);}
function startSolo(){myName=document.getElementById('nameInput').value.trim().toUpperCase()||'GHOST';hide('mainMenu');startGame(Date.now());}

// ── Navigation helpers ─────────────────────────────
function show(id){document.getElementById(id).style.display='flex';}
function hide(id){document.getElementById(id).style.display='none';}

function goToMenu(){
  gameState='menu';
  if(gameTimer){clearInterval(gameTimer);gameTimer=null;}
  if(animId){cancelAnimationFrame(animId);animId=null;}
  if(signalingWs){try{signalingWs.close();}catch(_){}}
  signalingWs=null;peers={};dataChannels={};pendingCandidates={};lobbyP={};
  localPlayer=null;players={};enemies=[];bullets=[];lootItems=[];particles=[];
  kills=0;sessionLoot=0;gameTime=ZONE_TIME;shootCD=0;isExtracting=false;extractionProgress=0;
  ['hud','minimap','joyL','joyR','connSt','wBar','swapBtn','exTimer','goScreen','lobbyScreen'].forEach(id=>{
    const el=document.getElementById(id);if(el)el.style.display='none';
  });
  ctx.clearRect(0,0,canvas.width,canvas.height);
  show('mainMenu');
  updMenuStats();
}

function goToShop(){
  hide('goScreen');
  openShop();
}

function openShop(){
  hide('mainMenu');
  renderShop();
  show('shopScreen');
}

function closeShop(){
  hide('shopScreen');
  show('mainMenu');
  updMenuStats();
}

// ── Shop ──────────────────────────────────────────
function renderShop(){
  document.getElementById('shopGold').textContent=profile.stash;
  document.getElementById('shopSpent').textContent=profile.totalSpent||0;
  const grid=document.getElementById('shopGrid');
  grid.innerHTML='';
  UPGRADES.forEach(u=>{
    const lvl=getUpgLvl(u.id);
    const maxed=lvl>=u.maxLvl;
    const cost=maxed?0:u.costs[lvl];
    const canAfford=!maxed&&profile.stash>=cost;
    const div=document.createElement('div');
    div.className='shop-item'+(maxed?' maxed':'');
    div.innerHTML=`<span class="si-lvl">${maxed?'MAX':lvl+'/'+u.maxLvl}</span>
      <div class="si-icon">${u.icon}</div>
      <div class="si-name">${u.name}</div>
      <div class="si-desc">${u.desc}</div>
      <div class="si-cost">${maxed?'✓ MAXED':canAfford?'💰 '+cost:'🔒 '+cost}</div>`;
    if(!maxed&&canAfford)div.onclick=()=>buyUpgrade(u.id);
    grid.appendChild(div);
  });
}

function buyUpgrade(id){
  const u=UPGRADES.find(x=>x.id===id);
  if(!u)return;
  const lvl=getUpgLvl(id);
  if(lvl>=u.maxLvl)return;
  const cost=u.costs[lvl];
  if(profile.stash<cost)return;
  profile.stash-=cost;
  profile.totalSpent=(profile.totalSpent||0)+cost;
  profile.upgrades[id]=(profile.upgrades[id]||0)+1;
  saveProfile();
  renderShop();
  showPopup(`${u.icon} ${u.name} UPGRADED!`);
}

// ── RNG ───────────────────────────────────────────
let rng=0;
function rnd(){rng=(rng*1664525+1013904223)&0xffffffff;return(rng>>>0)/0xffffffff;}

// ── Map ───────────────────────────────────────────
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
}
function solid(x,y){const tx=Math.floor(x/TILE),ty=Math.floor(y/TILE);if(tx<0||ty<0||tx>=MAP_W||ty>=MAP_H)return true;const t=tileMap[ty][tx];return t===1||t===3;}
function isCover(x,y){const tx=Math.floor(x/TILE),ty=Math.floor(y/TILE);if(tx<0||ty<0||tx>=MAP_W||ty>=MAP_H)return false;return tileMap[ty][tx]===2;}

// ── Random spawn + extraction placement ───────────
function findSpawnAndExtraction(){
  // Collect free floor tiles
  const free=[];
  for(let ty=1;ty<MAP_H-1;ty++)for(let tx=1;tx<MAP_W-1;tx++)if(tileMap[ty][tx]===0)free.push({tx,ty});
  // Pick random spawn
  let spawnTile=free[Math.floor(rnd()*free.length)];
  const sx=(spawnTile.tx+0.5)*TILE, sy=(spawnTile.ty+0.5)*TILE;
  // Find tile farthest from spawn
  let best=null,bestDist=0;
  for(const t of free){const d=Math.hypot(t.tx-spawnTile.tx,t.ty-spawnTile.ty);if(d>bestDist){bestDist=d;best=t;}}
  // Set extraction zone at that tile, clear area around it
  const ex=(best.tx+0.5)*TILE,ey=(best.ty+0.5)*TILE;
  for(let dy=-4;dy<=4;dy++)for(let dx=-4;dx<=4;dx++){
    const ty2=best.ty+dy,tx2=best.tx+dx;
    if(ty2>0&&ty2<MAP_H-1&&tx2>0&&tx2<MAP_W-1)tileMap[ty2][tx2]=4;
  }
  extractionZone={x:ex,y:ey,r:90};
  return{sx,sy};
}

// ── LOS ───────────────────────────────────────────
function hasLOS(x0,y0,x1,y1){
  let tx0=Math.floor(x0/TILE),ty0=Math.floor(y0/TILE);
  const tx1=Math.floor(x1/TILE),ty1=Math.floor(y1/TILE);
  const dx=Math.abs(tx1-tx0),dy=Math.abs(ty1-ty0);
  const sx=tx0<tx1?1:-1,sy=ty0<ty1?1:-1;
  let err=dx-dy;
  for(let s=0;s<90;s++){
    if(tx0===tx1&&ty0===ty1)return true;
    if(tx0<0||ty0<0||tx0>=MAP_W||ty0>=MAP_H)return false;
    const t=tileMap[ty0][tx0];if(t===1||t===3)return false;
    const e2=2*err;if(e2>-dy){err-=dy;tx0+=sx;}if(e2<dx){err+=dx;ty0+=sy;}
  }
  return false;
}

// ── Players ───────────────────────────────────────
function mkLocal(sx,sy){
  const maxHP=calcMaxHP();
  const armorHP=calcArmor();
  const weapons=[mkWep('pistol')];
  if(hasStarterGear()){const smg=mkWep('smg');smg.reserve+=50;weapons.push(smg);}
  return{id:myId,name:myName,x:sx,y:sy,aimAngle:0,
    health:maxHP,maxHealth:maxHP,armor:armorHP,maxArmor:armorHP,
    dead:false,radius:14,weapons,weaponIdx:0,color:'#00ffe7',isLocal:true};
}
function mkRemote(id,name){return{id,name,x:5*TILE,y:5*TILE,aimAngle:0,health:100,maxHealth:100,dead:false,radius:14,color:'#ff6b35',isLocal:false};}
function curWep(){return localPlayer?localPlayer.weapons[localPlayer.weaponIdx]:null;}

function cycleWep(){
  if(!localPlayer)return;
  localPlayer.weaponIdx=(localPlayer.weaponIdx+1)%localPlayer.weapons.length;
  shootCD=0;updWBar();updHUD();
}
function switchTo(i){if(!localPlayer||i>=localPlayer.weapons.length)return;localPlayer.weaponIdx=i;shootCD=0;updWBar();updHUD();}

function equipWeapon(key){
  if(!localPlayer)return;
  const ex=localPlayer.weapons.findIndex(w=>w.key===key);
  if(ex>=0){const w=localPlayer.weapons[ex];w.reserve=Math.min(w.reserve+WDEFS[key].res,WDEFS[key].res*3);showPopup(`${WDEFS[key].icon} AMMO REFILLED`);return;}
  if(localPlayer.weapons.length>=3)localPlayer.weapons[localPlayer.weaponIdx]=mkWep(key);
  else localPlayer.weapons.push(mkWep(key));
  updWBar();showPopup(`${WDEFS[key].icon} ${WDEFS[key].name} EQUIPPED`);
}

function updWBar(){
  if(!localPlayer)return;
  const bar=document.getElementById('wBar');bar.innerHTML='';
  localPlayer.weapons.forEach((w,i)=>{
    const d=document.createElement('div');d.className='ws'+(i===localPlayer.weaponIdx?' active':'');
    d.innerHTML=`<span class="wk">${i+1}</span><span class="wi">${w.icon}</span><span>${w.name}</span><span style="color:var(--gold);font-size:0.6rem">${w.ammo}/${w.reserve}</span>`;
    d.onclick=()=>switchTo(i);
    bar.appendChild(d);
  });
}

// ── Enemies ───────────────────────────────────────
function mkEnemy(x,y,type='grunt'){
  const T={grunt:{health:60,speed:55,damage:12,radius:13,color:'#ff2a2a',reward:1,sight:250,fr:900},
           heavy:{health:150,speed:32,damage:22,radius:18,color:'#ff6622',reward:3,sight:200,fr:1400},
           sniper:{health:40,speed:28,damage:40,radius:11,color:'#aa00ff',reward:2,sight:380,fr:2800}};
  const t=T[type]||T.grunt;
  return{id:'e_'+Math.random().toString(36).slice(2),x,y,type,...t,maxHealth:t.health,
    patrolAngle:rnd()*Math.PI*2,state:'patrol',lastShot:0,alertCD:0,lastKX:x,lastKY:y};
}
function spawnEnemies(sx,sy){
  enemies=[];
  for(let i=0;i<38;i++){
    let x,y,tr=0;
    do{x=(Math.floor(rnd()*(MAP_W-4))+2)*TILE+TILE/2;y=(Math.floor(rnd()*(MAP_H-4))+2)*TILE+TILE/2;tr++;}while(solid(x,y)&&tr<20);
    if(!solid(x,y)&&Math.hypot(x-sx,y-sy)>220){const r=rnd();enemies.push(mkEnemy(x,y,r<0.62?'grunt':r<0.86?'heavy':'sniper'));}
  }
}
function spawnLoot(sx,sy){
  lootItems=[];
  const wDrops=['smg','shotgun','rifle','sniper'];
  wDrops.forEach(wk=>{
    let x,y,tr=0;
    do{x=(Math.floor(rnd()*(MAP_W-6))+3)*TILE+TILE/2;y=(Math.floor(rnd()*(MAP_H-6))+3)*TILE+TILE/2;tr++;}while((solid(x,y)||Math.hypot(x-sx,y-sy)<100)&&tr<30);
    if(!solid(x,y)){lootItems.push({type:'weapon',icon:WDEFS[wk].icon,wkey:wk,color:WDEFS[wk].col,label:WDEFS[wk].name+' FOUND',x,y,id:'wl_'+wk,collected:false});}
  });
  const LT=[{type:'ammo',icon:'🔋',color:'#ffd700',label:'AMMO PACK'},{type:'health',icon:'💊',value:40,color:'#0f8',label:'+40 HP'},{type:'valuables',icon:'💎',value:5,color:'#a8f',label:'+5 LOOT'},{type:'bigLoot',icon:'📦',value:15,color:'#fa0',label:'+15 LOOT'}];
  for(let i=0;i<55;i++){
    let x,y,tr=0;
    do{x=(Math.floor(rnd()*(MAP_W-4))+2)*TILE+TILE/2;y=(Math.floor(rnd()*(MAP_H-4))+2)*TILE+TILE/2;tr++;}while(solid(x,y)&&tr<20);
    if(!solid(x,y)){const lt=LT[Math.floor(rnd()*LT.length)];lootItems.push({...lt,x,y,id:'l_'+i,collected:false});}
  }
}

// ── Bullets / Particles ───────────────────────────
function spawnBullet(x,y,angle,oid,spd,col,dmg,rng){bullets.push({x,y,vx:Math.cos(angle)*spd,vy:Math.sin(angle)*spd,ownerId:oid,life:rng,radius:oid.startsWith('e_')?3.5:4,color:col,damage:dmg});}
function spawnPart(x,y,col,n=8){for(let i=0;i<n;i++){const a=Math.random()*Math.PI*2,s=60+Math.random()*140;particles.push({x,y,vx:Math.cos(a)*s,vy:Math.sin(a)*s,life:0.4+Math.random()*0.4,maxLife:0.8,radius:2+Math.random()*3,color:col});}}

// ── Camera ────────────────────────────────────────
let cam={x:0,y:0};
function updCam(){if(!localPlayer)return;cam.x=Math.max(0,Math.min(localPlayer.x-canvas.width/2,MAP_W*TILE-canvas.width));cam.y=Math.max(0,Math.min(localPlayer.y-canvas.height/2,MAP_H*TILE-canvas.height));}

// ── Joysticks ─────────────────────────────────────
const jL={active:false,sx:0,sy:0,dx:0,dy:0,id:-1};
const jR={active:false,sx:0,sy:0,dx:0,dy:0,id:-1};
const JR=54;
let joyInited=false;
function initJoy(){
  if(joyInited)return;joyInited=true;
  const eL=document.getElementById('joyL'),eR=document.getElementById('joyR');
  const tL=document.getElementById('tL'),tR=document.getElementById('tR');
  document.addEventListener('touchstart',e=>{
    if(gameState!=='playing')return;
    e.preventDefault();
    for(const t of e.changedTouches){
      const rl=eL.getBoundingClientRect(),rr=eR.getBoundingClientRect();
      if(t.clientX>rl.left-40&&t.clientX<rl.right+40&&t.clientY>rl.top-40&&t.clientY<rl.bottom+40){
        jL.active=true;jL.id=t.identifier;jL.sx=rl.left+rl.width/2;jL.sy=rl.top+rl.height/2;tL.style.boxShadow='0 0 18px rgba(0,255,231,0.9)';
      }else if(t.clientX>rr.left-40&&t.clientX<rr.right+40&&t.clientY>rr.top-40&&t.clientY<rr.bottom+40){
        jR.active=true;jR.id=t.identifier;jR.sx=rr.left+rr.width/2;jR.sy=rr.top+rr.height/2;tR.style.boxShadow='0 0 18px rgba(0,255,231,0.9)';
      }
    }
  },{passive:false});
  document.addEventListener('touchmove',e=>{
    if(gameState!=='playing')return;
    e.preventDefault();
    for(const t of e.changedTouches){
      if(jL.active&&t.identifier===jL.id){const dx=t.clientX-jL.sx,dy=t.clientY-jL.sy,d=Math.hypot(dx,dy),cl=Math.min(d,JR),a=Math.atan2(dy,dx);jL.dx=Math.cos(a)*cl/JR;jL.dy=Math.sin(a)*cl/JR;tL.style.transform=`translate(calc(-50% + ${Math.cos(a)*cl}px),calc(-50% + ${Math.sin(a)*cl}px))`;}
      if(jR.active&&t.identifier===jR.id){const dx=t.clientX-jR.sx,dy=t.clientY-jR.sy,d=Math.hypot(dx,dy),cl=Math.min(d,JR),a=Math.atan2(dy,dx);jR.dx=Math.cos(a)*cl/JR;jR.dy=Math.sin(a)*cl/JR;tR.style.transform=`translate(calc(-50% + ${Math.cos(a)*cl}px),calc(-50% + ${Math.sin(a)*cl}px))`;}
    }
  },{passive:false});
  const rst=e=>{for(const t of e.changedTouches){
    if(jL.id===t.identifier){jL.active=false;jL.dx=0;jL.dy=0;jL.id=-1;tL.style.transform='translate(-50%,-50%)';tL.style.boxShadow='0 0 10px rgba(0,255,231,0.4)';}
    if(jR.id===t.identifier){jR.active=false;jR.dx=0;jR.dy=0;jR.id=-1;tR.style.transform='translate(-50%,-50%)';tR.style.boxShadow='0 0 10px rgba(0,255,231,0.4)';}
  }};
  document.addEventListener('touchend',rst,{passive:false});
  document.addEventListener('touchcancel',rst,{passive:false});
}

// ── Keyboard ──────────────────────────────────────
const keys={};
document.addEventListener('keydown',e=>{
  keys[e.code]=true;
  if(gameState!=='playing')return;
  if(e.code==='Space'||e.code==='KeyF')doShoot();
  if(e.code==='KeyR')doReload();
  if(e.code==='KeyQ')cycleWep();
  if(e.code==='Digit1')switchTo(0);
  if(e.code==='Digit2')switchTo(1);
  if(e.code==='Digit3')switchTo(2);
});
document.addEventListener('keyup',e=>{keys[e.code]=false;});
canvas.addEventListener('mousemove',e=>{if(!localPlayer)return;const r=canvas.getBoundingClientRect();localPlayer.aimAngle=Math.atan2(e.clientY-r.top-(localPlayer.y-cam.y),e.clientX-r.left-(localPlayer.x-cam.x));});
canvas.addEventListener('mousedown',e=>{if(e.button===0)doShoot();});

// ── Game start ────────────────────────────────────
function startGame(seed){
  gameState='playing';kills=0;sessionLoot=0;gameTime=ZONE_TIME;shootCD=0;isExtracting=false;extractionProgress=0;
  genMap(seed);
  const{sx,sy}=findSpawnAndExtraction();
  localPlayer=mkLocal(sx,sy);players[myId]=localPlayer;
  spawnEnemies(sx,sy);spawnLoot(sx,sy);

  ['hud','minimap','connSt','joyL','joyR','wBar','swapBtn'].forEach(id=>{
    const el=document.getElementById(id);if(el)el.style.display=id==='hud'?'flex':'block';
  });
  document.getElementById('swapBtn').style.display='flex';
  document.getElementById('wBar').style.display='flex';
  hide('goScreen');hide('lobbyScreen');hide('mainMenu');hide('shopScreen');

  resize();initJoy();updHUD();updWBar();updCam();
  gameTimer=setInterval(()=>{gameTime--;updZoneT();if(gameTime<=0)playerDead('ZONE');},1000);
  lastT=performance.now();
  if(animId)cancelAnimationFrame(animId);
  animId=requestAnimationFrame(loop);
}

function loop(now){
  const dt=Math.min((now-lastT)/1000,0.05);lastT=now;
  if(gameState==='playing'){update(dt);draw();}
  animId=requestAnimationFrame(loop);
}

// ── Update ────────────────────────────────────────
function update(dt){
  if(!localPlayer||localPlayer.dead)return;
  shootCD-=dt*1000;
  // Move
  let mx=0,my=0;
  if(keys['KeyW']||keys['ArrowUp'])my-=1;if(keys['KeyS']||keys['ArrowDown'])my+=1;
  if(keys['KeyA']||keys['ArrowLeft'])mx-=1;if(keys['KeyD']||keys['ArrowRight'])mx+=1;
  mx+=jL.dx;my+=jL.dy;
  const ml=Math.hypot(mx,my);if(ml>1){mx/=ml;my/=ml;}
  const spd=calcSpeed()*dt,r=localPlayer.radius;
  const nx=localPlayer.x+mx*spd,ny=localPlayer.y+my*spd;
  if(!solid(nx+r,localPlayer.y)&&!solid(nx-r,localPlayer.y))localPlayer.x=nx;
  if(!solid(localPlayer.x,ny+r)&&!solid(localPlayer.x,ny-r))localPlayer.y=ny;
  // Right joystick aim+shoot
  if(jR.active&&(Math.abs(jR.dx)>0.1||Math.abs(jR.dy)>0.1)){localPlayer.aimAngle=Math.atan2(jR.dy,jR.dx);doShoot();}
  // Reload timer
  const w=curWep();
  if(w&&w.reloading&&performance.now()-w.reloadStart>=w.reloadTime*calcReloadMult()){
    const need=WDEFS[w.key].ammo-w.ammo,take=Math.min(need,w.reserve);w.ammo+=take;w.reserve-=take;w.reloading=false;updHUD();updWBar();
  }
  updCam();updEnemies(dt);updBullets(dt);
  for(let i=particles.length-1;i>=0;i--){const p=particles[i];p.x+=p.vx*dt;p.y+=p.vy*dt;p.life-=dt;p.vx*=0.92;p.vy*=0.92;if(p.life<=0)particles.splice(i,1);}
  for(const l of lootItems)if(!l.collected&&Math.hypot(l.x-localPlayer.x,l.y-localPlayer.y)<30)collectLoot(l);
  // Extraction
  const dex=Math.hypot(localPlayer.x-extractionZone.x,localPlayer.y-extractionZone.y);
  if(dex<extractionZone.r){
    if(!isExtracting){isExtracting=true;extractionProgress=0;}
    extractionProgress+=dt*1000;
    document.getElementById('exTimer').style.display='block';
    document.getElementById('exTimer').innerHTML=`⬆ EXTRACTING<br>${Math.ceil((EXTRACTION_TIME-extractionProgress)/1000)}s`;
    if(extractionProgress>=EXTRACTION_TIME)playerExtracted();
  }else{isExtracting=false;extractionProgress=0;document.getElementById('exTimer').style.display='none';}
  if(Object.keys(dataChannels).length>0)broadcast({type:'state',id:myId,name:myName,x:localPlayer.x,y:localPlayer.y,a:localPlayer.aimAngle,h:localPlayer.health,dead:localPlayer.dead});
  updHUD();
}

function doShoot(){
  if(!localPlayer||localPlayer.dead)return;
  const w=curWep();if(!w||w.reloading)return;
  if(w.ammo<=0){doReload();return;}
  if(shootCD>0)return;
  shootCD=w.fireRate;w.ammo--;
  const dmg=Math.round(w.damage*calcDmgMult());
  for(let i=0;i<w.burst;i++){
    const a=localPlayer.aimAngle+(Math.random()-0.5)*w.spread*2;
    spawnBullet(localPlayer.x,localPlayer.y,a,myId,w.bulletSpd,w.color,dmg,w.range);
    broadcast({type:'shoot',x:localPlayer.x,y:localPlayer.y,a,id:myId,spd:w.bulletSpd,col:w.color,dmg,rng:w.range});
  }
  spawnPart(localPlayer.x+Math.cos(localPlayer.aimAngle)*16,localPlayer.y+Math.sin(localPlayer.aimAngle)*16,'#ffffaa',3);
  if(w.ammo<=0)doReload();
  updWBar();
}
function doReload(){const w=curWep();if(!w||w.reloading||w.reserve<=0||w.ammo>=WDEFS[w.key].ammo)return;w.reloading=true;w.reloadStart=performance.now();}

function updEnemies(dt){
  const allP=Object.values(players).filter(p=>!p.dead);
  for(const en of enemies){
    if(en.health<=0)continue;
    en.alertCD=Math.max(0,(en.alertCD||0)-dt);
    let near=null,nd=Infinity,vis=false;
    for(const p of allP){const d=Math.hypot(p.x-en.x,p.y-en.y);if(d<nd){nd=d;near=p;vis=(d<=en.sight&&hasLOS(en.x,en.y,p.x,p.y));}}
    if(!near)continue;
    const ang=Math.atan2(near.y-en.y,near.x-en.x);
    if(vis){en.lastKX=near.x;en.lastKY=near.y;en.alertCD=3.5;en.state='chase';}
    else if(en.alertCD>0)en.state='search';
    else en.state='patrol';
    let ma=en.patrolAngle;
    if(en.state==='chase'){ma=ang;}
    else if(en.state==='search'){ma=Math.atan2(en.lastKY-en.y,en.lastKX-en.x);if(Math.hypot(en.x-en.lastKX,en.y-en.lastKY)<20)en.alertCD=0;}
    else en.patrolAngle+=dt*0.4;
    const nx=en.x+Math.cos(ma)*en.speed*dt,ny=en.y+Math.sin(ma)*en.speed*dt;
    if(!solid(nx,ny)){en.x=nx;en.y=ny;}else en.patrolAngle+=0.35;
    if((isHost||Object.keys(peers).length===0)&&vis&&nd<en.sight*0.95){
      en.lastShot=(en.lastShot||0)+dt*1000;
      if(en.lastShot>en.fr){en.lastShot=0;
        const sp=en.type==='sniper'?0.02:0.12;
        spawnBullet(en.x,en.y,ang+(Math.random()-0.5)*sp,en.id,
          en.type==='sniper'?700:en.type==='heavy'?280:380,
          en.type==='sniper'?'#d4f':en.type==='heavy'?'#f84':'#f40',en.damage,1.6);
      }
    }
  }
}

function updBullets(dt){
  for(let i=bullets.length-1;i>=0;i--){
    const b=bullets[i];b.x+=b.vx*dt;b.y+=b.vy*dt;b.life-=dt;
    if(b.life<=0||solid(b.x,b.y)){spawnPart(b.x,b.y,b.color,4);bullets.splice(i,1);continue;}
    if(isCover(b.x,b.y)){spawnPart(b.x,b.y,'#888',3);bullets.splice(i,1);continue;}
    if(b.ownerId===myId){
      let hit=false;
      for(let j=enemies.length-1;j>=0;j--){
        const en=enemies[j];if(en.health<=0)continue;
        if(Math.hypot(b.x-en.x,b.y-en.y)<en.radius+b.radius){
          en.health-=b.damage;spawnPart(en.x,en.y,en.color,8);bullets.splice(i,1);hit=true;
          if(en.health<=0){kills++;sessionLoot+=en.reward;addKF(`${curWep()?.icon||''} ${en.type.toUpperCase()}`);spawnPart(en.x,en.y,en.color,20);
            lootItems.push({type:'valuables',icon:'💰',value:en.reward,color:'#fd0',label:`+${en.reward}`,x:en.x,y:en.y,id:'el_'+en.id,collected:false});
            broadcast({type:'enemy_dead',id:en.id});}break;
        }
      }
      if(!hit)Object.values(players).forEach(p=>{if(p.isLocal||p.dead)return;if(Math.hypot(b.x-p.x,b.y-p.y)<p.radius+b.radius)broadcast({type:'hit',target:p.id,killer:myId,dmg:b.damage});});
    }
    if(b.ownerId.startsWith('e_')&&localPlayer&&!localPlayer.dead){
      if(Math.hypot(b.x-localPlayer.x,b.y-localPlayer.y)<localPlayer.radius+b.radius){
        // Armor absorbs damage first
        let dmgLeft=b.damage;
        if(localPlayer.armor>0){const ab=Math.min(localPlayer.armor,dmgLeft);localPlayer.armor-=ab;dmgLeft-=ab;}
        if(dmgLeft>0)localPlayer.health-=dmgLeft;
        showHit();spawnPart(localPlayer.x,localPlayer.y,'#f22',6);
        bullets.splice(i,1);broadcast({type:'hit',target:myId,killer:b.ownerId,dmg:b.damage});
        if(localPlayer.health<=0)playerDead('ENEMY');
      }
    }
  }
}

function collectLoot(l){
  l.collected=true;
  if(l.type==='ammo'){const w=curWep();if(w){w.reserve=Math.min(w.reserve+WDEFS[w.key].res,WDEFS[w.key].res*3);showPopup('🔋 AMMO');}
  }else if(l.type==='health'){localPlayer.health=Math.min(localPlayer.health+l.value,localPlayer.maxHealth);showPopup(l.label);
  }else if(l.type==='weapon'){equipWeapon(l.wkey);
  }else{sessionLoot+=l.value;showPopup(l.label);}
  spawnPart(l.x,l.y,l.color,12);broadcast({type:'loot_taken',lootId:l.id});
  updHUD();updWBar();
}

let popT=null;
function showPopup(t){const el=document.getElementById('lootPop');el.textContent=t;el.style.opacity='1';if(popT)clearTimeout(popT);popT=setTimeout(()=>el.style.opacity='0',1600);}
function showHit(){canvas.style.filter='brightness(2) saturate(0)';setTimeout(()=>canvas.style.filter='',80);}
let kfArr=[];
function addKF(t){const el=document.getElementById('killfeed'),e=document.createElement('div');e.className='kf';e.textContent=t;el.prepend(e);kfArr.push(e);if(kfArr.length>5){kfArr[0].remove();kfArr.shift();}setTimeout(()=>{try{e.remove();}catch(_){}},4000);}

// ── Draw ──────────────────────────────────────────
const TC={0:'#0a1520',1:'#1a2535',2:'#162030',3:'#061520',4:'#0a1f10'};

function draw(){
  resize();ctx.clearRect(0,0,canvas.width,canvas.height);
  ctx.save();ctx.translate(-cam.x,-cam.y);
  drawMap();drawEZ();drawLoot();drawEnemies();drawBullets();drawParts();drawPlayers();
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
  ctx.fillStyle='rgba(0,255,100,0.9)';ctx.font='bold 11px monospace';ctx.textAlign='center';ctx.fillText('EXTRACTION',x,y-r-10);
  if(isExtracting&&extractionProgress>0){ctx.beginPath();ctx.arc(x,y,r+8,-Math.PI/2,-Math.PI/2+Math.PI*2*(extractionProgress/EXTRACTION_TIME));ctx.strokeStyle='#fd0';ctx.lineWidth=4;ctx.stroke();}
}
function drawLoot(){
  for(const l of lootItems){
    if(l.collected)continue;
    const bob=Math.sin(Date.now()*0.003+l.x)*3;
    ctx.shadowColor=l.color;ctx.shadowBlur=l.type==='weapon'?16:10;
    ctx.beginPath();ctx.arc(l.x,l.y+bob,l.type==='weapon'?13:9,0,Math.PI*2);ctx.fillStyle=l.color+'33';ctx.fill();
    if(l.type==='weapon'){ctx.beginPath();ctx.arc(l.x,l.y+bob,13,0,Math.PI*2);ctx.strokeStyle=l.color+'99';ctx.lineWidth=2;ctx.stroke();}
    ctx.shadowBlur=0;ctx.font='15px serif';ctx.textAlign='center';ctx.fillText(l.icon,l.x,l.y+bob+5);
  }
}
function drawEnemies(){
  for(const en of enemies){
    if(en.health<=0)continue;
    const{x,y,radius:r,color,health,maxHealth}=en;
    ctx.beginPath();ctx.ellipse(x,y+r*0.8,r*0.7,r*0.3,0,0,Math.PI*2);ctx.fillStyle='rgba(0,0,0,0.4)';ctx.fill();
    ctx.beginPath();ctx.arc(x,y,r,0,Math.PI*2);ctx.fillStyle=color;ctx.fill();ctx.strokeStyle='rgba(0,0,0,0.5)';ctx.lineWidth=2;ctx.stroke();
    if(en.state==='chase'){ctx.beginPath();ctx.arc(x,y,r+5,0,Math.PI*2);ctx.strokeStyle='rgba(255,60,0,0.7)';ctx.lineWidth=1.5;ctx.stroke();}
    else if(en.state==='search'){ctx.beginPath();ctx.arc(x,y,r+5,0,Math.PI*2);ctx.strokeStyle='rgba(255,200,0,0.5)';ctx.lineWidth=1;ctx.setLineDash([3,3]);ctx.stroke();ctx.setLineDash([]);}
    if(en.state!=='patrol'&&localPlayer){const a=Math.atan2(localPlayer.y-y,localPlayer.x-x);ctx.beginPath();ctx.arc(x+Math.cos(a)*r*0.4,y+Math.sin(a)*r*0.4,3,0,Math.PI*2);ctx.fillStyle='#ff0';ctx.fill();}
    if(health<maxHealth){const bw=r*2;ctx.fillStyle='rgba(0,0,0,0.6)';ctx.fillRect(x-bw/2,y-r-9,bw,4);ctx.fillStyle=health>maxHealth/2?'#0f8':'#f22';ctx.fillRect(x-bw/2,y-r-9,bw*(health/maxHealth),4);}
    ctx.font='8px monospace';ctx.textAlign='center';ctx.fillStyle='rgba(255,255,255,0.35)';ctx.fillText(en.type[0].toUpperCase(),x,y+r+10);
  }
}
function drawBullets(){
  for(const b of bullets){ctx.beginPath();ctx.arc(b.x,b.y,b.radius,0,Math.PI*2);ctx.fillStyle=b.color;ctx.shadowColor=b.color;ctx.shadowBlur=8;ctx.fill();ctx.shadowBlur=0;ctx.beginPath();ctx.moveTo(b.x,b.y);ctx.lineTo(b.x-b.vx*0.028,b.y-b.vy*0.028);ctx.strokeStyle=b.color+'77';ctx.lineWidth=2;ctx.stroke();}
}
function drawParts(){
  for(const p of particles){const a=p.life/(p.maxLife||0.8);ctx.beginPath();ctx.arc(p.x,p.y,p.radius*a,0,Math.PI*2);ctx.fillStyle=p.color+Math.floor(a*255).toString(16).padStart(2,'0');ctx.fill();}
}
function drawPlayers(){Object.values(players).forEach(p=>drawPlayer(p));}
function drawPlayer(p){
  if(p.dead)return;
  const{x,y,radius:r,color,aimAngle,health,maxHealth,name,isLocal}=p;
  ctx.beginPath();ctx.ellipse(x,y+r*0.8,r*0.7,r*0.3,0,0,Math.PI*2);ctx.fillStyle='rgba(0,0,0,0.4)';ctx.fill();
  if(isLocal){ctx.shadowColor=color;ctx.shadowBlur=14;}
  ctx.beginPath();ctx.arc(x,y,r,0,Math.PI*2);ctx.fillStyle=isLocal?'#0a1520':'#1a0a00';ctx.fill();ctx.strokeStyle=color;ctx.lineWidth=isLocal?2.5:2;ctx.stroke();ctx.shadowBlur=0;
  const gl=r+14;ctx.beginPath();ctx.moveTo(x+Math.cos(aimAngle)*r,y+Math.sin(aimAngle)*r);ctx.lineTo(x+Math.cos(aimAngle)*gl,y+Math.sin(aimAngle)*gl);ctx.strokeStyle=color;ctx.lineWidth=3;ctx.stroke();
  if(isLocal&&curWep()){ctx.font='11px serif';ctx.textAlign='center';ctx.fillText(curWep().icon,x,y-r-17);}
  ctx.font='9px monospace';ctx.fillStyle=isLocal?'rgba(0,255,231,0.8)':'rgba(255,100,50,0.8)';ctx.fillText(name,x,y-r-5);
  const bw=r*2.5;ctx.fillStyle='rgba(0,0,0,0.6)';ctx.fillRect(x-bw/2,y-r-3,bw,3);ctx.fillStyle=isLocal?(health>30?'#0f8':'#f22'):'#f64';ctx.fillRect(x-bw/2,y-r-3,bw*(health/maxHealth),3);
  // Armor bar
  if(isLocal&&p.armor>0&&p.maxArmor>0){ctx.fillStyle='rgba(0,0,0,0.5)';ctx.fillRect(x-bw/2,y-r-1,bw,2);ctx.fillStyle='#4af';ctx.fillRect(x-bw/2,y-r-1,bw*(p.armor/p.maxArmor),2);}
  if(isLocal){const w=curWep();if(w&&w.reloading){const pct=(performance.now()-w.reloadStart)/(w.reloadTime*calcReloadMult());ctx.beginPath();ctx.arc(x,y,r+7,-Math.PI/2,-Math.PI/2+Math.PI*2*pct);ctx.strokeStyle='#fd0';ctx.lineWidth=3;ctx.stroke();}}
}
function drawMM(){
  mmCtx.clearRect(0,0,108,108);mmCtx.fillStyle='rgba(0,5,10,0.88)';mmCtx.fillRect(0,0,108,108);
  const sx=108/(MAP_W*TILE),sy=108/(MAP_H*TILE);
  for(let ty=0;ty<MAP_H;ty++)for(let tx=0;tx<MAP_W;tx++){const t=tileMap[ty][tx];
    if(t===1){mmCtx.fillStyle='#2a3a50';mmCtx.fillRect(tx*TILE*sx,ty*TILE*sy,TILE*sx+.5,TILE*sy+.5);}
    if(t===4){mmCtx.fillStyle='rgba(0,200,80,0.4)';mmCtx.fillRect(tx*TILE*sx,ty*TILE*sy,TILE*sx+.5,TILE*sy+.5);}
  }
  if(hasRadar()){for(const en of enemies){if(en.health<=0)continue;mmCtx.beginPath();mmCtx.arc(en.x*sx,en.y*sy,1.5,0,Math.PI*2);mmCtx.fillStyle=en.state==='chase'?'#f40':'#f22';mmCtx.fill();}}
  for(const l of lootItems){if(l.collected)continue;mmCtx.beginPath();mmCtx.arc(l.x*sx,l.y*sy,l.type==='weapon'?2.5:1,0,Math.PI*2);mmCtx.fillStyle=l.type==='weapon'?l.color:'#fd0';mmCtx.fill();}
  Object.values(players).filter(p=>!p.isLocal&&!p.dead).forEach(p=>{mmCtx.beginPath();mmCtx.arc(p.x*sx,p.y*sy,2.5,0,Math.PI*2);mmCtx.fillStyle='#f64';mmCtx.fill();});
  if(localPlayer){mmCtx.beginPath();mmCtx.arc(localPlayer.x*sx,localPlayer.y*sy,3,0,Math.PI*2);mmCtx.fillStyle='#0fe';mmCtx.fill();}
  mmCtx.strokeStyle='rgba(0,255,231,0.25)';mmCtx.lineWidth=0.5;mmCtx.strokeRect(cam.x*sx,cam.y*sy,canvas.width*sx,canvas.height*sy);
}

// ── HUD updates ───────────────────────────────────
function updHUD(){
  if(!localPlayer)return;
  document.getElementById('hpVal').textContent=Math.max(0,Math.floor(localPlayer.health));
  document.getElementById('hFill').style.width=Math.max(0,localPlayer.health/localPlayer.maxHealth*100)+'%';
  document.getElementById('armorFill').style.width=localPlayer.maxArmor>0?Math.max(0,localPlayer.armor/localPlayer.maxArmor*100)+'%':'0%';
  document.getElementById('lootVal').textContent=sessionLoot;
  document.getElementById('killC').textContent=kills;
  const w=curWep();document.getElementById('ammoD').textContent=w?`${w.ammo}/${w.reserve}${w.reloading?' ↺':''}`:'-';
}
function updZoneT(){const m=Math.floor(gameTime/60).toString().padStart(2,'0'),s=(gameTime%60).toString().padStart(2,'0');document.getElementById('zoneT').textContent=`${m}:${s}`;if(gameTime<=30)document.getElementById('zoneT').style.color='#f22';}
function setConn(c,t){document.getElementById('connDot').className='dot '+c;document.getElementById('connTxt').textContent=t;}

// ── End states ────────────────────────────────────
function playerDead(killer){
  if(gameState!=='playing')return;
  gameState='dead';localPlayer.dead=true;clearInterval(gameTimer);gameTimer=null;
  // Insurance: keep 30% of loot on death
  let kept=0;
  if(hasInsurance()){kept=Math.floor(sessionLoot*0.3);profile.stash+=kept;}
  // No bonus for death — weapons/loot are lost
  profile.totalRuns++;profile.totalKills+=kills;profile.totalLoot+=sessionLoot;saveProfile();
  ['hud','minimap','joyL','joyR','connSt','wBar','swapBtn','exTimer'].forEach(id=>{const el=document.getElementById(id);if(el)el.style.display='none';});
  document.getElementById('goTitle').textContent='ELIMINATED';document.getElementById('goTitle').className='got dead';
  document.getElementById('goStats').textContent=`KILLS: ${kills}  ·  LOOT: ${sessionLoot}  ·  BY: ${killer}`;
  document.getElementById('goStash').textContent=kept>0?`💰 INSURANCE KEPT ${kept} LOOT  (STASH: ${profile.stash})`:`💰 STASH: ${profile.stash}`;
  show('goScreen');
}

function playerExtracted(){
  if(gameState!=='playing')return;
  gameState='won';clearInterval(gameTimer);gameTimer=null;
  // Add session loot to stash on successful extraction
  profile.stash+=sessionLoot;
  profile.totalRuns++;profile.totalKills+=kills;profile.totalExtractions++;profile.totalLoot+=sessionLoot;saveProfile();
  ['hud','minimap','joyL','joyR','connSt','wBar','swapBtn','exTimer'].forEach(id=>{const el=document.getElementById(id);if(el)el.style.display='none';});
  document.getElementById('goTitle').textContent='EXTRACTED';document.getElementById('goTitle').className='got win';
  document.getElementById('goStats').textContent=`KILLS: ${kills}  ·  LOOT: ${sessionLoot}  ·  TIME: ${ZONE_TIME-gameTime}s`;
  document.getElementById('goStash').textContent=`💰 STASH: ${profile.stash}  (+${sessionLoot})`;
  show('goScreen');
}

function updMenuStats(){
  const el=document.getElementById('menuStats');
  if(!el)return;
  el.textContent=`💰 STASH: ${profile.stash}  |  RUNS: ${profile.totalRuns}  |  EXTRACTIONS: ${profile.totalExtractions||0}`;
}

// ── PWA ───────────────────────────────────────────
function setupPWA(){
  function mkIcon(size){const c=document.createElement('canvas');c.width=c.height=size;const x=c.getContext('2d');x.fillStyle='#040a0f';x.fillRect(0,0,size,size);x.beginPath();x.arc(size/2,size/2,size*0.44,0,Math.PI*2);x.strokeStyle='#00ffe7';x.lineWidth=size*0.04;x.stroke();x.strokeStyle='#00ffe7';x.lineWidth=size*0.06;x.beginPath();x.moveTo(size/2,size*0.25);x.lineTo(size/2,size*0.75);x.stroke();x.beginPath();x.moveTo(size*0.25,size/2);x.lineTo(size*0.75,size/2);x.stroke();x.fillStyle='#ffd700';[0,1,2,3].forEach(i=>{const a=i*Math.PI/2+Math.PI/4;x.beginPath();x.arc(size/2+Math.cos(a)*size*0.32,size/2+Math.sin(a)*size*0.32,size*0.06,0,Math.PI*2);x.fill();});x.fillStyle='#00ffe7';x.font=`bold ${size*0.13}px monospace`;x.textAlign='center';x.textBaseline='middle';x.fillText('EXFIL',size/2,size*0.5);return c.toDataURL('image/png');}
  const i192=mkIcon(192),i512=mkIcon(512);
  const manifest={name:'EXFIL // ZERO',short_name:'EXFIL',start_url:'./',display:'fullscreen',orientation:'landscape',background_color:'#040a0f',theme_color:'#040a0f',icons:[{src:i192,sizes:'192x192',type:'image/png',purpose:'any maskable'},{src:i512,sizes:'512x512',type:'image/png',purpose:'any maskable'}]};
  const blob=new Blob([JSON.stringify(manifest)],{type:'application/manifest+json'});
  const link=document.createElement('link');link.rel='manifest';link.href=URL.createObjectURL(blob);document.head.appendChild(link);
  const al=document.createElement('link');al.rel='apple-touch-icon';al.href=i192;document.head.appendChild(al);
  const isIOS=/iphone|ipad|ipod/i.test(navigator.userAgent);
  const isSA=window.navigator.standalone===true||window.matchMedia('(display-mode:fullscreen)').matches;
  if(isIOS&&!isSA&&!sessionStorage.getItem('ih')){sessionStorage.setItem('ih','1');setTimeout(()=>{const h=document.createElement('div');h.style.cssText='position:fixed;bottom:18px;left:50%;transform:translateX(-50%);background:rgba(0,20,30,0.96);border:1px solid #0fe;color:#0fe;font-family:monospace;font-size:0.68rem;line-height:1.6;padding:10px 16px;z-index:9999;text-align:center;max-width:270px;box-shadow:0 0 18px rgba(0,255,231,0.25);';h.innerHTML='📲 Safari → Teilen → Zum Home-Bildschirm<br><small style="opacity:0.4">Tippen zum Schließen</small>';h.onclick=()=>h.remove();document.body.appendChild(h);setTimeout(()=>{if(h.parentNode)h.remove();},8000);},2500);}
  window.addEventListener('beforeinstallprompt',e=>{e.preventDefault();const b=document.createElement('div');b.style.cssText='position:fixed;bottom:18px;left:50%;transform:translateX(-50%);background:rgba(0,20,30,0.96);border:1px solid #fd0;color:#fd0;font-family:monospace;font-size:0.78rem;padding:10px 18px;z-index:9999;cursor:pointer;letter-spacing:0.12em;white-space:nowrap;box-shadow:0 0 18px rgba(255,215,0,0.25);';b.textContent='⬇ AUF HOME-SCREEN INSTALLIEREN';b.onclick=()=>{e.prompt();b.remove();};document.body.appendChild(b);setTimeout(()=>{if(b.parentNode)b.remove();},8000);});
}

window.addEventListener('load',async()=>{
  resize();
  await initDB();
  await loadProfile();
  setupPWA();
});
document.addEventListener('contextmenu',e=>e.preventDefault());
</script>
</body>
</html>
