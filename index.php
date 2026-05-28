<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<title>EXFIL // ZERO</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&display=swap');

  :root {
    --neon: #00ffe7;
    --red: #ff2a2a;
    --gold: #ffd700;
    --dark: #040a0f;
    --panel: rgba(0,20,30,0.92);
    --border: rgba(0,255,231,0.3);
  }

  * { margin:0; padding:0; box-sizing:border-box; }

  body {
    background: var(--dark);
    font-family: 'Share Tech Mono', monospace;
    color: var(--neon);
    overflow: hidden;
    height: 100vh;
    width: 100vw;
    touch-action: none;
    user-select: none;
  }

  #mainMenu, #lobbyScreen, #gameOverScreen {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    background: radial-gradient(ellipse at center, #001a24 0%, #040a0f 70%);
    z-index: 100;
  }

  .logo {
    font-family: 'Orbitron', sans-serif;
    font-weight: 900;
    font-size: clamp(2.5rem, 8vw, 5rem);
    letter-spacing: 0.2em;
    color: var(--neon);
    text-shadow: 0 0 20px var(--neon), 0 0 60px rgba(0,255,231,0.4);
    animation: pulse 2s ease-in-out infinite;
  }

  .tagline {
    font-size: 0.9rem;
    letter-spacing: 0.5em;
    color: rgba(0,255,231,0.5);
    margin: 0.5rem 0 3rem;
    text-transform: uppercase;
  }

  @keyframes pulse {
    0%,100% { text-shadow: 0 0 20px var(--neon), 0 0 60px rgba(0,255,231,0.4); }
    50% { text-shadow: 0 0 40px var(--neon), 0 0 100px rgba(0,255,231,0.6), 0 0 3px #fff; }
  }

  .btn {
    background: transparent;
    border: 1px solid var(--neon);
    color: var(--neon);
    font-family: 'Share Tech Mono', monospace;
    font-size: 1rem;
    letter-spacing: 0.2em;
    padding: 0.8rem 2.5rem;
    cursor: pointer;
    margin: 0.4rem;
    transition: all 0.2s;
    text-transform: uppercase;
    min-width: 220px;
    position: relative;
    overflow: hidden;
  }

  .btn::before {
    content:'';
    position:absolute; inset:0;
    background: var(--neon);
    transform: translateX(-101%);
    transition: transform 0.2s;
    z-index: -1;
  }

  .btn:hover::before, .btn:active::before { transform: translateX(0); }
  .btn:hover, .btn:active { color: var(--dark); }

  .btn.danger { border-color: var(--red); color: var(--red); }
  .btn.danger::before { background: var(--red); }
  .btn.gold { border-color: var(--gold); color: var(--gold); }
  .btn.gold::before { background: var(--gold); }

  input[type=text] {
    background: rgba(0,255,231,0.05);
    border: 1px solid var(--border);
    color: var(--neon);
    font-family: 'Share Tech Mono', monospace;
    font-size: 1rem;
    padding: 0.7rem 1rem;
    width: 260px;
    letter-spacing: 0.1em;
    margin: 0.4rem;
    outline: none;
    text-align: center;
  }

  input[type=text]:focus { border-color: var(--neon); box-shadow: 0 0 10px rgba(0,255,231,0.3); }
  input::placeholder { color: rgba(0,255,231,0.3); }

  #gameCanvas {
    position: absolute; inset: 0;
    display: block;
    image-rendering: pixelated;
  }

  /* HUD */
  #hud {
    position: absolute; top: 0; left: 0; right: 0;
    padding: 12px 16px;
    display: flex; align-items: flex-start; justify-content: space-between;
    pointer-events: none;
    z-index: 10;
  }

  .hud-panel {
    background: var(--panel);
    border: 1px solid var(--border);
    padding: 8px 14px;
    backdrop-filter: blur(4px);
    min-width: 140px;
  }

  .hud-label {
    font-size: 0.6rem;
    letter-spacing: 0.3em;
    color: rgba(0,255,231,0.5);
    margin-bottom: 3px;
  }

  .hud-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
  }

  #healthBar {
    width: 100%; height: 4px;
    background: rgba(255,42,42,0.2);
    margin-top: 5px;
  }

  #healthFill {
    height: 100%;
    background: var(--red);
    transition: width 0.2s;
    box-shadow: 0 0 6px var(--red);
  }

  #ammoDisplay { color: var(--gold); }

  /* Loot popup */
  #lootPopup {
    position: absolute; bottom: 160px; left: 50%;
    transform: translateX(-50%);
    background: var(--panel);
    border: 1px solid var(--gold);
    color: var(--gold);
    padding: 8px 20px;
    font-size: 0.85rem;
    letter-spacing: 0.1em;
    z-index: 20;
    opacity: 0;
    transition: opacity 0.3s;
    text-align: center;
    pointer-events: none;
  }

  /* Minimap */
  #minimap {
    position: absolute; top: 10px; right: 10px;
    width: 110px; height: 110px;
    border: 1px solid var(--border);
    background: rgba(0,10,15,0.8);
    z-index: 10;
  }

  /* Inventory */
  #inventory {
    position: absolute; bottom: 160px; left: 16px;
    z-index: 10;
    pointer-events: none;
  }

  .inv-item {
    display: inline-block;
    width: 44px; height: 44px;
    border: 1px solid var(--border);
    background: var(--panel);
    margin: 2px;
    position: relative;
    text-align: center;
    line-height: 44px;
    font-size: 1.2rem;
  }

  .inv-item.active { border-color: var(--neon); box-shadow: 0 0 8px rgba(0,255,231,0.5); }

  /* Extraction zone indicator */
  #extractionTimer {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    color: var(--gold);
    text-shadow: 0 0 20px var(--gold);
    z-index: 30;
    display: none;
    text-align: center;
  }

  /* JOYSTICKS */
  #joystickLeft, #joystickRight {
    position: absolute; bottom: 30px;
    width: 110px; height: 110px;
    border-radius: 50%;
    background: rgba(0,255,231,0.06);
    border: 2px solid rgba(0,255,231,0.25);
    z-index: 50;
    touch-action: none;
  }

  #joystickLeft { left: 30px; }
  #joystickRight { right: 30px; }

  .joystick-thumb {
    position: absolute;
    width: 44px; height: 44px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0,255,231,0.7), rgba(0,255,231,0.2));
    border: 2px solid var(--neon);
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 12px rgba(0,255,231,0.5);
    transition: box-shadow 0.1s;
  }

  .joystick-thumb.active {
    box-shadow: 0 0 20px rgba(0,255,231,0.9);
  }

  /* Kill feed */
  #killfeed {
    position: absolute; right: 130px; top: 10px;
    z-index: 20; pointer-events: none;
  }

  .kill-entry {
    background: rgba(255,42,42,0.15);
    border-left: 2px solid var(--red);
    padding: 3px 8px;
    font-size: 0.75rem;
    margin-bottom: 3px;
    animation: fadeIn 0.2s ease;
  }

  @keyframes fadeIn { from { opacity:0; transform: translateX(20px); } to { opacity:1; transform: none; } }

  /* Connection status */
  #connStatus {
    position: absolute; bottom: 155px; right: 16px;
    font-size: 0.7rem;
    letter-spacing: 0.1em;
    color: rgba(0,255,231,0.5);
    z-index: 20;
  }

  .dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #888; margin-right: 5px; }
  .dot.green { background: #00ff88; box-shadow: 0 0 6px #00ff88; }
  .dot.yellow { background: var(--gold); }
  .dot.red { background: var(--red); }

  #gameOverScreen { display: none; }
  .go-title { font-family:'Orbitron',sans-serif; font-size: 3rem; font-weight:900; margin-bottom: 1rem; }
  .go-title.dead { color: var(--red); text-shadow: 0 0 30px var(--red); }
  .go-title.win { color: var(--gold); text-shadow: 0 0 30px var(--gold); }

  .lobby-code {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    color: var(--gold);
    letter-spacing: 0.4em;
    padding: 1rem 2rem;
    border: 1px solid var(--gold);
    margin: 1rem 0;
    text-shadow: 0 0 15px var(--gold);
  }

  .lobby-info {
    font-size: 0.8rem;
    color: rgba(0,255,231,0.6);
    margin: 0.5rem 0;
    letter-spacing: 0.15em;
  }

  #playerList {
    margin: 1rem 0;
    text-align: center;
  }

  .player-entry {
    padding: 4px 20px;
    border-left: 2px solid var(--neon);
    margin: 4px 0;
    font-size: 0.9rem;
    color: var(--neon);
  }

  .scanline {
    position: fixed; inset: 0;
    background: repeating-linear-gradient(
      0deg, transparent, transparent 2px,
      rgba(0,0,0,0.08) 2px, rgba(0,0,0,0.08) 4px
    );
    pointer-events: none; z-index: 999;
  }

  #debugLog {
    position: absolute; bottom: 155px; left: 16px;
    font-size: 0.6rem; color: rgba(0,255,231,0.3);
    z-index: 5; pointer-events: none;
    max-width: 250px;
  }
</style>
</head>
<body>

<div class="scanline"></div>
<canvas id="gameCanvas"></canvas>

<!-- MAIN MENU -->
<div id="mainMenu">
  <div class="logo">EXFIL // ZERO</div>
  <div class="tagline">Extract or Die Trying</div>
  <input type="text" id="nameInput" placeholder="CALL SIGN" maxlength="12">
  <button class="btn gold" onclick="createLobby()">HOST GAME</button>
  <button class="btn" onclick="showJoinScreen()">JOIN GAME</button>
  <button class="btn" onclick="startSolo()">SOLO RUN</button>
  <div id="joinSection" style="display:none; flex-direction:column; align-items:center; margin-top:0.5rem;">
    <input type="text" id="codeInput" placeholder="ROOM CODE" maxlength="6" style="text-transform:uppercase">
    <button class="btn" onclick="joinLobby()">CONNECT</button>
  </div>
</div>

<!-- LOBBY -->
<div id="lobbyScreen" style="display:none">
  <div class="logo" style="font-size:2rem">STAGING AREA</div>
  <div class="lobby-info">SHARE THIS CODE</div>
  <div class="lobby-code" id="lobbyCode">------</div>
  <div class="lobby-info" id="lobbyStatusText">WAITING FOR PLAYERS...</div>
  <div id="playerList"></div>
  <button class="btn gold" id="startBtn" style="display:none" onclick="hostStartGame()">DEPLOY</button>
  <button class="btn danger" onclick="leaveLobby()">ABORT</button>
</div>

<!-- HUD -->
<div id="hud" style="display:none">
  <div class="hud-panel">
    <div class="hud-label">HEALTH</div>
    <div class="hud-value" id="healthVal" style="color:var(--red)">100</div>
    <div id="healthBar"><div id="healthFill" style="width:100%"></div></div>
  </div>
  <div class="hud-panel" style="text-align:center">
    <div class="hud-label">LOOT</div>
    <div class="hud-value" style="color:var(--gold)" id="lootVal">0</div>
    <div class="hud-label" style="margin-top:4px">ZONE</div>
    <div class="hud-value" id="zoneTimer" style="font-size:0.9rem">--:--</div>
  </div>
  <div class="hud-panel" style="text-align:right">
    <div class="hud-label">AMMO</div>
    <div class="hud-value" id="ammoDisplay">30 / 90</div>
    <div class="hud-label" style="margin-top:4px">KILLS</div>
    <div class="hud-value" id="killCount">0</div>
  </div>
</div>

<canvas id="minimap" width="110" height="110" style="display:none"></canvas>
<div id="killfeed"></div>
<div id="connStatus" style="display:none"><span class="dot" id="connDot"></span><span id="connText">OFFLINE</span></div>
<div id="inventory" style="display:none"></div>
<div id="lootPopup"></div>
<div id="extractionTimer"></div>
<div id="joystickLeft" style="display:none"><div class="joystick-thumb" id="thumbL"></div></div>
<div id="joystickRight" style="display:none"><div class="joystick-thumb" id="thumbR"></div></div>
<div id="debugLog"></div>

<!-- GAME OVER -->
<div id="gameOverScreen">
  <div class="go-title" id="goTitle">ELIMINATED</div>
  <div style="font-size:1rem; letter-spacing:0.2em; margin:1rem 0; color:rgba(0,255,231,0.7)" id="goStats"></div>
  <button class="btn gold" onclick="location.reload()">MAIN MENU</button>
</div>

<script>
// ═══════════════════════════════════════════════════════════
// EXFIL // ZERO — Full Game Engine
// ═══════════════════════════════════════════════════════════

const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const minimap = document.getElementById('minimap');
const minimapCtx = minimap.getContext('2d');

// ─── Constants ───────────────────────────────────────────
const TILE = 40;
const MAP_W = 64, MAP_H = 64;
const PLAYER_SPEED = 180;
const BULLET_SPEED = 520;
const ENEMY_SPEED = 60;
const MAX_HEALTH = 100;
const EXTRACTION_TIME = 5000; // ms to stand in zone
const ZONE_CLOSE_TIME = 120; // seconds

// ─── IndexedDB ───────────────────────────────────────────
let db;
function initDB() {
  return new Promise(res => {
    const req = indexedDB.open('ExfilZero', 2);
    req.onupgradeneeded = e => {
      const d = e.target.result;
      if (!d.objectStoreNames.contains('profile')) d.createObjectStore('profile', { keyPath: 'id' });
      if (!d.objectStoreNames.contains('runs')) d.createObjectStore('runs', { autoIncrement: true });
    };
    req.onsuccess = e => { db = e.target.result; res(); };
    req.onerror = () => res();
  });
}

function dbSet(store, value) {
  if (!db) return;
  const tx = db.transaction(store, 'readwrite');
  tx.objectStore(store).put(value);
}

function dbGet(store, key) {
  return new Promise(res => {
    if (!db) return res(null);
    const tx = db.transaction(store, 'readonly');
    const req = tx.objectStore(store).get(key);
    req.onsuccess = () => res(req.result);
    req.onerror = () => res(null);
  });
}

// ─── Game State ──────────────────────────────────────────
let gameState = 'menu'; // menu | lobby | playing | dead | won
let localPlayer = null;
let players = {};      // id -> player obj
let enemies = [];
let bullets = [];
let lootItems = [];
let particles = [];
let tileMap = [];
let extractionZone = { x: 0, y: 0, r: 80 };
let gameTime = ZONE_CLOSE_TIME;
let gameTimer = null;
let myId = 'local_' + Math.random().toString(36).slice(2,8);
let myName = 'GHOST';
let kills = 0;
let lootCollected = 0;
let extractionProgress = 0;
let isExtracting = false;

// ─── P2P / WebRTC ────────────────────────────────────────
let isHost = false;
let roomCode = '';
let peers = {}; // id -> RTCPeerConnection
let dataChannels = {}; // id -> RTCDataChannel
let signalingWs = null;
let pendingCandidates = {};

// We use a free public signaling server approach via PeerJS-style signaling
// Fallback: manual SDP exchange via lobby code display
// For simplicity: use a WebSocket signaling relay via wss://socketsbay.com or similar
// Since no server needed for GAME data, only signaling (offer/answer/ICE)
// We'll use a simple relay

const SIGNAL_URL = 'wss://socketsbay.com/wss/v2/1/demo/';
// Note: This is a public demo WS — real app would use own signaling

function connectSignaling() {
  try {
    signalingWs = new WebSocket(SIGNAL_URL);
    signalingWs.onopen = () => setConnStatus('yellow', 'SIGNALING');
    signalingWs.onmessage = e => handleSignal(JSON.parse(e.data));
    signalingWs.onerror = () => setConnStatus('red', 'SIG ERROR');
    signalingWs.onclose = () => {
      if (gameState === 'playing') setConnStatus('red', 'DISCONNECTED');
    };
  } catch(ex) {
    setConnStatus('red', 'NO SIGNAL');
  }
}

function sendSignal(msg) {
  if (signalingWs && signalingWs.readyState === 1) {
    signalingWs.send(JSON.stringify({ room: roomCode, ...msg }));
  }
}

async function handleSignal(msg) {
  if (msg.room !== roomCode) return;
  if (msg.from === myId) return;

  if (msg.type === 'join') {
    if (isHost) {
      // Send offer to new peer
      await createPeerConnection(msg.from, true);
    }
    updateLobbyPlayers(msg.from, msg.name);
  }
  if (msg.type === 'offer') {
    await createPeerConnection(msg.from, false);
    await peers[msg.from].setRemoteDescription(new RTCSessionDescription(msg.sdp));
    const answer = await peers[msg.from].createAnswer();
    await peers[msg.from].setLocalDescription(answer);
    sendSignal({ type: 'answer', from: myId, to: msg.from, sdp: answer });
    flushCandidates(msg.from);
  }
  if (msg.type === 'answer' && peers[msg.from]) {
    await peers[msg.from].setRemoteDescription(new RTCSessionDescription(msg.sdp));
    flushCandidates(msg.from);
  }
  if (msg.type === 'ice' && peers[msg.from]) {
    try { await peers[msg.from].addIceCandidate(new RTCIceCandidate(msg.candidate)); } catch(e){}
  }
  if (msg.type === 'start') {
    // Host broadcasts seed + start
    if (!isHost) {
      Math.seedrandom_val = msg.seed;
      startGame(msg.seed);
    }
  }
  if (msg.type === 'playerList') {
    msg.players.forEach(p => updateLobbyPlayers(p.id, p.name));
  }
}

async function createPeerConnection(peerId, initiator) {
  if (peers[peerId]) return;
  const pc = new RTCPeerConnection({
    iceServers: [
      { urls: 'stun:stun.l.google.com:19302' },
      { urls: 'stun:stun1.l.google.com:19302' }
    ]
  });
  peers[peerId] = pc;
  pendingCandidates[peerId] = [];

  pc.onicecandidate = e => {
    if (e.candidate) sendSignal({ type: 'ice', from: myId, to: peerId, candidate: e.candidate });
  };

  pc.onconnectionstatechange = () => {
    if (pc.connectionState === 'connected') setConnStatus('green', 'P2P LINKED');
    if (pc.connectionState === 'disconnected') handlePeerDisconnect(peerId);
  };

  if (initiator) {
    const dc = pc.createDataChannel('game', { ordered: false, maxRetransmits: 0 });
    setupDataChannel(dc, peerId);
    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);
    sendSignal({ type: 'offer', from: myId, to: peerId, sdp: offer });
  } else {
    pc.ondatachannel = e => setupDataChannel(e.channel, peerId);
  }
}

function flushCandidates(peerId) {
  if (pendingCandidates[peerId]) {
    pendingCandidates[peerId].forEach(c => {
      try { peers[peerId].addIceCandidate(new RTCIceCandidate(c)); } catch(e){}
    });
    pendingCandidates[peerId] = [];
  }
}

function setupDataChannel(dc, peerId) {
  dataChannels[peerId] = dc;
  dc.onopen = () => {
    setConnStatus('green', 'CONNECTED');
    // Send own player data
    sendToPeer(peerId, { type: 'hello', id: myId, name: myName });
  };
  dc.onmessage = e => handlePeerMessage(peerId, JSON.parse(e.data));
  dc.onerror = err => console.warn('DC error', err);
}

function sendToPeer(peerId, msg) {
  const dc = dataChannels[peerId];
  if (dc && dc.readyState === 'open') {
    try { dc.send(JSON.stringify(msg)); } catch(e){}
  }
}

function broadcast(msg) {
  Object.keys(dataChannels).forEach(id => sendToPeer(id, msg));
}

function handlePeerMessage(peerId, msg) {
  if (msg.type === 'hello') {
    players[msg.id] = players[msg.id] || createRemotePlayer(msg.id, msg.name);
  }
  if (msg.type === 'state') {
    if (!players[msg.id]) players[msg.id] = createRemotePlayer(msg.id, msg.name || '???');
    const p = players[msg.id];
    p.x = msg.x; p.y = msg.y;
    p.aimAngle = msg.a;
    p.health = msg.h;
    p.dead = msg.dead;
    p.name = msg.name || p.name;
  }
  if (msg.type === 'shoot') {
    spawnBullet(msg.x, msg.y, msg.a, msg.id, false);
  }
  if (msg.type === 'hit') {
    if (msg.target === myId && localPlayer) {
      localPlayer.health -= msg.dmg;
      showHit();
      if (localPlayer.health <= 0) playerDead(msg.killer);
    }
  }
  if (msg.type === 'loot_taken') {
    lootItems = lootItems.filter(l => l.id !== msg.lootId);
  }
  if (msg.type === 'enemy_dead') {
    const en = enemies.find(e => e.id === msg.id);
    if (en) en.health = 0;
  }
}

function handlePeerDisconnect(peerId) {
  if (players[peerId]) players[peerId].dead = true;
  setConnStatus('yellow', 'PEER LEFT');
}

// ─── Lobby UI ────────────────────────────────────────────
let lobbyPlayers = {};

function updateLobbyPlayers(id, name) {
  lobbyPlayers[id] = name;
  renderLobbyList();
}

function renderLobbyList() {
  const el = document.getElementById('playerList');
  el.innerHTML = Object.entries(lobbyPlayers).map(([id,n]) =>
    `<div class="player-entry">▶ ${n} ${id===myId?'(YOU)':''}</div>`).join('');
  const count = Object.keys(lobbyPlayers).length;
  document.getElementById('lobbyStatusText').textContent =
    `${count} OPERATOR${count!==1?'S':''} STAGED`;
  if (isHost && count >= 1) document.getElementById('startBtn').style.display = '';
}

function showJoinScreen() {
  const s = document.getElementById('joinSection');
  s.style.display = s.style.display === 'none' ? 'flex' : 'none';
}

async function createLobby() {
  myName = document.getElementById('nameInput').value.trim().toUpperCase() || 'GHOST';
  isHost = true;
  roomCode = Math.random().toString(36).slice(2,8).toUpperCase();
  lobbyPlayers = {};
  updateLobbyPlayers(myId, myName);

  document.getElementById('mainMenu').style.display = 'none';
  document.getElementById('lobbyScreen').style.display = 'flex';
  document.getElementById('lobbyCode').textContent = roomCode;

  connectSignaling();
  setTimeout(() => sendSignal({ type: 'join', from: myId, name: myName }), 800);
}

function joinLobby() {
  myName = document.getElementById('nameInput').value.trim().toUpperCase() || 'SHADOW';
  roomCode = document.getElementById('codeInput').value.trim().toUpperCase();
  if (!roomCode) return;
  isHost = false;
  lobbyPlayers = {};
  updateLobbyPlayers(myId, myName);

  document.getElementById('mainMenu').style.display = 'none';
  document.getElementById('lobbyScreen').style.display = 'flex';
  document.getElementById('lobbyCode').textContent = roomCode;

  connectSignaling();
  setTimeout(() => sendSignal({ type: 'join', from: myId, name: myName }), 800);
}

function leaveLobby() {
  if (signalingWs) signalingWs.close();
  location.reload();
}

function hostStartGame() {
  const seed = Date.now();
  sendSignal({ type: 'start', from: myId, seed });
  Math.seedrandom_val = seed;
  startGame(seed);
}

function startSolo() {
  myName = document.getElementById('nameInput').value.trim().toUpperCase() || 'GHOST';
  isHost = false;
  document.getElementById('mainMenu').style.display = 'none';
  startGame(Date.now());
}

// ─── Seeded Random ───────────────────────────────────────
let rngState = 0;
function seededRand() {
  rngState = (rngState * 1664525 + 1013904223) & 0xffffffff;
  return ((rngState >>> 0) / 0xffffffff);
}

// ─── Map Generation ──────────────────────────────────────
// Tile types: 0=floor 1=wall 2=cover 3=water 4=extraction
function generateMap(seed) {
  rngState = seed;
  tileMap = [];
  for (let y = 0; y < MAP_H; y++) {
    tileMap[y] = [];
    for (let x = 0; x < MAP_W; x++) {
      if (x === 0 || y === 0 || x === MAP_W-1 || y === MAP_H-1) {
        tileMap[y][x] = 1;
      } else {
        const r = seededRand();
        tileMap[y][x] = r < 0.12 ? 1 : (r < 0.18 ? 2 : 0);
      }
    }
  }
  // Rooms
  for (let i = 0; i < 12; i++) {
    const rx = Math.floor(seededRand() * (MAP_W-14)) + 4;
    const ry = Math.floor(seededRand() * (MAP_H-14)) + 4;
    const rw = Math.floor(seededRand() * 8) + 5;
    const rh = Math.floor(seededRand() * 8) + 5;
    for (let dy = ry; dy < ry+rh; dy++)
      for (let dx = rx; dx < rx+rw; dx++)
        if (dy > 0 && dy < MAP_H-1 && dx > 0 && dx < MAP_W-1)
          tileMap[dy][dx] = 0;
    // Walls around
    for (let dy = ry-1; dy <= ry+rh; dy++)
      for (let dx = rx-1; dx <= rx+rw; dx++) {
        if (dy < 1 || dy >= MAP_H-1 || dx < 1 || dx >= MAP_W-1) continue;
        if ((dy === ry-1 || dy === ry+rh || dx === rx-1 || dx === rx+rw) && tileMap[dy][dx] === 0)
          tileMap[dy][dx] = 1;
      }
  }

  // Extraction zone (far corner)
  const ez = {
    x: (MAP_W - 8) * TILE,
    y: (MAP_H - 8) * TILE,
    r: 90
  };
  extractionZone = ez;
  // Clear tiles around extraction
  for (let dy = MAP_H-11; dy < MAP_H-2; dy++)
    for (let dx = MAP_W-11; dx < MAP_W-2; dx++)
      if (tileMap[dy] && tileMap[dy][dx] !== undefined) tileMap[dy][dx] = 4;
}

function isSolid(x, y) {
  const tx = Math.floor(x / TILE);
  const ty = Math.floor(y / TILE);
  if (tx < 0 || ty < 0 || tx >= MAP_W || ty >= MAP_H) return true;
  const t = tileMap[ty][tx];
  return t === 1 || t === 3;
}

function isCover(x, y) {
  const tx = Math.floor(x / TILE);
  const ty = Math.floor(y / TILE);
  if (tx < 0 || ty < 0 || tx >= MAP_W || ty >= MAP_H) return false;
  return tileMap[ty][tx] === 2;
}

// ─── Player ──────────────────────────────────────────────
function createLocalPlayer() {
  return {
    id: myId, name: myName,
    x: 3 * TILE, y: 3 * TILE,
    vx: 0, vy: 0,
    aimAngle: 0,
    health: MAX_HEALTH,
    maxHealth: MAX_HEALTH,
    dead: false,
    radius: 14,
    weapon: { ammo: 30, reserve: 90, fireRate: 120, lastShot: 0, reloading: false, reloadTime: 1800, reloadStart: 0 },
    loot: 0,
    kills: 0,
    color: '#00ffe7',
    isLocal: true
  };
}

function createRemotePlayer(id, name) {
  return {
    id, name,
    x: 5 * TILE, y: 5 * TILE,
    aimAngle: 0,
    health: MAX_HEALTH,
    maxHealth: MAX_HEALTH,
    dead: false,
    radius: 14,
    color: '#ff6b35',
    isLocal: false
  };
}

function createEnemy(x, y, type = 'grunt') {
  const types = {
    grunt: { health: 60, speed: 55, damage: 10, radius: 13, color: '#ff2a2a', reward: 1 },
    heavy: { health: 150, speed: 35, damage: 20, radius: 18, color: '#ff6622', reward: 3 },
    sniper: { health: 40, speed: 30, damage: 35, radius: 11, color: '#aa00ff', reward: 2 }
  };
  const t = types[type] || types.grunt;
  return {
    id: 'e_' + Math.random().toString(36).slice(2),
    x, y, type,
    health: t.health, maxHealth: t.health,
    speed: t.speed, damage: t.damage,
    radius: t.radius, color: t.color,
    reward: t.reward,
    shootTimer: seededRand() * 2000,
    patrolAngle: seededRand() * Math.PI * 2,
    state: 'patrol', // patrol | chase | shoot
    lastShotTime: 0,
    fireRate: type === 'sniper' ? 2500 : (type === 'heavy' ? 1200 : 800)
  };
}

function spawnEnemies() {
  enemies = [];
  for (let i = 0; i < 35; i++) {
    let x, y, tries = 0;
    do {
      x = (Math.floor(seededRand() * (MAP_W-4)) + 2) * TILE + TILE/2;
      y = (Math.floor(seededRand() * (MAP_H-4)) + 2) * TILE + TILE/2;
      tries++;
    } while (isSolid(x, y) && tries < 20);
    const dist = Math.hypot(x - localPlayer.x, y - localPlayer.y);
    if (dist > 200 && !isSolid(x, y)) {
      const r = seededRand();
      const type = r < 0.65 ? 'grunt' : (r < 0.88 ? 'heavy' : 'sniper');
      enemies.push(createEnemy(x, y, type));
    }
  }
}

function spawnLoot() {
  lootItems = [];
  const lootTypes = [
    { type: 'ammo', icon: '🔋', value: 30, color: '#ffd700', label: '+30 AMMO' },
    { type: 'health', icon: '💊', value: 40, color: '#00ff88', label: '+40 HP' },
    { type: 'valuables', icon: '💎', value: 5, color: '#a78bfa', label: '+5 LOOT' },
    { type: 'bigLoot', icon: '📦', value: 15, color: '#f59e0b', label: '+15 LOOT' }
  ];
  for (let i = 0; i < 50; i++) {
    let x, y, tries = 0;
    do {
      x = (Math.floor(seededRand() * (MAP_W-4)) + 2) * TILE + TILE/2;
      y = (Math.floor(seededRand() * (MAP_H-4)) + 2) * TILE + TILE/2;
      tries++;
    } while (isSolid(x, y) && tries < 20);
    if (!isSolid(x, y)) {
      const lt = lootTypes[Math.floor(seededRand() * lootTypes.length)];
      lootItems.push({ ...lt, x, y, id: 'l_' + i, collected: false });
    }
  }
}

// ─── Bullet ──────────────────────────────────────────────
function spawnBullet(x, y, angle, ownerId, isLocal) {
  bullets.push({
    x, y,
    vx: Math.cos(angle) * BULLET_SPEED,
    vy: Math.sin(angle) * BULLET_SPEED,
    ownerId, isLocal,
    life: 1.5,
    radius: 4,
    color: ownerId.startsWith('e_') ? '#ff4400' : (isLocal ? '#fff' : '#ffaa00')
  });
}

// ─── Particles ───────────────────────────────────────────
function spawnParticles(x, y, color, count = 8) {
  for (let i = 0; i < count; i++) {
    const a = Math.random() * Math.PI * 2;
    const spd = 60 + Math.random() * 140;
    particles.push({
      x, y,
      vx: Math.cos(a) * spd,
      vy: Math.sin(a) * spd,
      life: 0.4 + Math.random() * 0.4,
      maxLife: 0.8,
      radius: 2 + Math.random() * 3,
      color
    });
  }
}

// ─── Camera ──────────────────────────────────────────────
let cam = { x: 0, y: 0 };

function updateCamera() {
  if (!localPlayer) return;
  const tw = canvas.width, th = canvas.height;
  cam.x = localPlayer.x - tw / 2;
  cam.y = localPlayer.y - th / 2;
  cam.x = Math.max(0, Math.min(cam.x, MAP_W * TILE - tw));
  cam.y = Math.max(0, Math.min(cam.y, MAP_H * TILE - th));
}

// ─── Joystick ────────────────────────────────────────────
const joyL = { active: false, startX: 0, startY: 0, dx: 0, dy: 0, id: -1 };
const joyR = { active: false, startX: 0, startY: 0, dx: 0, dy: 0, id: -1 };
const JOY_R = 55;

function initJoysticks() {
  const elL = document.getElementById('joystickLeft');
  const elR = document.getElementById('joystickRight');
  const thumbL = document.getElementById('thumbL');
  const thumbR = document.getElementById('thumbR');

  function getJoyRect(el) { return el.getBoundingClientRect(); }

  canvas.addEventListener('touchstart', e => {
    e.preventDefault();
    for (const t of e.changedTouches) {
      const rl = getJoyRect(elL), rr = getJoyRect(elR);
      if (t.clientX > rl.left-30 && t.clientX < rl.right+30 && t.clientY > rl.top-30 && t.clientY < rl.bottom+30) {
        joyL.active = true; joyL.id = t.identifier;
        joyL.startX = rl.left + rl.width/2; joyL.startY = rl.top + rl.height/2;
        thumbL.classList.add('active');
      } else if (t.clientX > rr.left-30 && t.clientX < rr.right+30 && t.clientY > rr.top-30 && t.clientY < rr.bottom+30) {
        joyR.active = true; joyR.id = t.identifier;
        joyR.startX = rr.left + rr.width/2; joyR.startY = rr.top + rr.height/2;
        thumbR.classList.add('active');
      }
    }
  }, { passive: false });

  canvas.addEventListener('touchmove', e => {
    e.preventDefault();
    for (const t of e.changedTouches) {
      if (joyL.active && t.identifier === joyL.id) {
        const dx = t.clientX - joyL.startX, dy = t.clientY - joyL.startY;
        const dist = Math.hypot(dx, dy);
        const clamped = Math.min(dist, JOY_R);
        const angle = Math.atan2(dy, dx);
        joyL.dx = Math.cos(angle) * clamped / JOY_R;
        joyL.dy = Math.sin(angle) * clamped / JOY_R;
        thumbL.style.transform = `translate(calc(-50% + ${Math.cos(angle)*clamped}px), calc(-50% + ${Math.sin(angle)*clamped}px))`;
      }
      if (joyR.active && t.identifier === joyR.id) {
        const dx = t.clientX - joyR.startX, dy = t.clientY - joyR.startY;
        const dist = Math.hypot(dx, dy);
        const clamped = Math.min(dist, JOY_R);
        const angle = Math.atan2(dy, dx);
        joyR.dx = Math.cos(angle) * clamped / JOY_R;
        joyR.dy = Math.sin(angle) * clamped / JOY_R;
        thumbR.style.transform = `translate(calc(-50% + ${Math.cos(angle)*clamped}px), calc(-50% + ${Math.sin(angle)*clamped}px))`;
      }
    }
  }, { passive: false });

  const resetJoy = e => {
    for (const t of e.changedTouches) {
      if (joyL.id === t.identifier) {
        joyL.active = false; joyL.dx = 0; joyL.dy = 0; joyL.id = -1;
        thumbL.style.transform = 'translate(-50%,-50%)';
        thumbL.classList.remove('active');
      }
      if (joyR.id === t.identifier) {
        joyR.active = false; joyR.dx = 0; joyR.dy = 0; joyR.id = -1;
        thumbR.style.transform = 'translate(-50%,-50%)';
        thumbR.classList.remove('active');
      }
    }
  };
  canvas.addEventListener('touchend', resetJoy, { passive: false });
  canvas.addEventListener('touchcancel', resetJoy, { passive: false });
}

// ─── Keyboard (Desktop) ──────────────────────────────────
const keys = {};
let mouseDx = 0, mouseDy = 0;
document.addEventListener('keydown', e => keys[e.code] = true);
document.addEventListener('keyup', e => keys[e.code] = false);
canvas.addEventListener('mousemove', e => {
  if (!localPlayer) return;
  const rect = canvas.getBoundingClientRect();
  const mx = e.clientX - rect.left;
  const my = e.clientY - rect.top;
  localPlayer.aimAngle = Math.atan2(
    my - (localPlayer.y - cam.y),
    mx - (localPlayer.x - cam.x)
  );
});
canvas.addEventListener('mousedown', e => {
  if (e.button === 0) handleShoot();
});

// ─── Main Game Loop ───────────────────────────────────────
let lastTime = 0;
let animId = null;

function startGame(seed) {
  document.getElementById('lobbyScreen').style.display = 'none';
  document.getElementById('mainMenu').style.display = 'none';
  document.getElementById('hud').style.display = 'flex';
  document.getElementById('minimap').style.display = 'block';
  document.getElementById('inventory').style.display = 'block';
  document.getElementById('connStatus').style.display = 'block';
  document.getElementById('joystickLeft').style.display = 'block';
  document.getElementById('joystickRight').style.display = 'block';

  gameState = 'playing';
  generateMap(seed);
  localPlayer = createLocalPlayer();
  players[myId] = localPlayer;
  spawnEnemies();
  spawnLoot();
  updateHUD();
  resizeCanvas();
  initJoysticks();

  // Keyboard fire
  document.addEventListener('keydown', e => {
    if (e.code === 'Space' || e.code === 'KeyF') handleShoot();
    if (e.code === 'KeyR') startReload();
  });

  gameTimer = setInterval(() => {
    gameTime--;
    updateZoneHUD();
    if (gameTime <= 0) playerDead('ZONE');
  }, 1000);

  lastTime = performance.now();
  if (animId) cancelAnimationFrame(animId);
  animId = requestAnimationFrame(gameLoop);
}

function gameLoop(now) {
  const dt = Math.min((now - lastTime) / 1000, 0.05);
  lastTime = now;

  if (gameState === 'playing') {
    update(dt);
    draw();
  }
  animId = requestAnimationFrame(gameLoop);
}

// ─── Update ──────────────────────────────────────────────
let shootCooldown = 0;

function update(dt) {
  if (!localPlayer || localPlayer.dead) return;

  shootCooldown -= dt * 1000;

  // Movement (WASD / left joystick)
  let mx = 0, my = 0;
  if (keys['KeyW'] || keys['ArrowUp']) my -= 1;
  if (keys['KeyS'] || keys['ArrowDown']) my += 1;
  if (keys['KeyA'] || keys['ArrowLeft']) mx -= 1;
  if (keys['KeyD'] || keys['ArrowRight']) mx += 1;
  mx += joyL.dx;
  my += joyL.dy;

  // Normalize
  const ml = Math.hypot(mx, my);
  if (ml > 1) { mx /= ml; my /= ml; }

  const spd = PLAYER_SPEED * dt;
  const nx = localPlayer.x + mx * spd;
  const ny = localPlayer.y + my * spd;
  const r = localPlayer.radius;

  // Collision
  if (!isSolid(nx + r, localPlayer.y) && !isSolid(nx - r, localPlayer.y)) localPlayer.x = nx;
  if (!isSolid(localPlayer.x, ny + r) && !isSolid(localPlayer.x, ny - r)) localPlayer.y = ny;

  // Right joystick = aim + auto-shoot
  if (joyR.active && (Math.abs(joyR.dx) > 0.1 || Math.abs(joyR.dy) > 0.1)) {
    localPlayer.aimAngle = Math.atan2(joyR.dy, joyR.dx);
    handleShoot();
  }

  // Reload
  const w = localPlayer.weapon;
  if (w.reloading) {
    if (now() - w.reloadStart >= w.reloadTime) {
      const needed = 30 - w.ammo;
      const take = Math.min(needed, w.reserve);
      w.ammo += take;
      w.reserve -= take;
      w.reloading = false;
    }
  }

  updateCamera();

  // Enemies
  updateEnemies(dt);

  // Bullets
  updateBullets(dt);

  // Particles
  for (let i = particles.length - 1; i >= 0; i--) {
    const p = particles[i];
    p.x += p.vx * dt; p.y += p.vy * dt;
    p.life -= dt;
    p.vx *= 0.92; p.vy *= 0.92;
    if (p.life <= 0) particles.splice(i, 1);
  }

  // Loot pickup
  for (const loot of lootItems) {
    if (loot.collected) continue;
    const dist = Math.hypot(loot.x - localPlayer.x, loot.y - localPlayer.y);
    if (dist < 28) {
      collectLoot(loot);
    }
  }

  // Extraction zone
  const distEx = Math.hypot(localPlayer.x - extractionZone.x, localPlayer.y - extractionZone.y);
  if (distEx < extractionZone.r) {
    if (!isExtracting) {
      isExtracting = true;
      extractionProgress = 0;
    }
    extractionProgress += dt * 1000;
    const pct = extractionProgress / EXTRACTION_TIME;
    document.getElementById('extractionTimer').style.display = 'block';
    document.getElementById('extractionTimer').innerHTML =
      `⬆ EXTRACTING<br>${Math.ceil((EXTRACTION_TIME - extractionProgress)/1000)}s`;
    if (extractionProgress >= EXTRACTION_TIME) playerExtracted();
  } else {
    isExtracting = false;
    extractionProgress = 0;
    document.getElementById('extractionTimer').style.display = 'none';
  }

  // Broadcast state
  if (Object.keys(dataChannels).length > 0) {
    broadcast({
      type: 'state', id: myId, name: myName,
      x: localPlayer.x, y: localPlayer.y,
      a: localPlayer.aimAngle,
      h: localPlayer.health,
      dead: localPlayer.dead
    });
  }

  updateHUD();
}

function now() { return performance.now(); }

function handleShoot() {
  if (!localPlayer || localPlayer.dead) return;
  const w = localPlayer.weapon;
  if (w.reloading) return;
  if (w.ammo <= 0) { startReload(); return; }
  if (shootCooldown > 0) return;
  shootCooldown = w.fireRate;
  w.ammo--;

  spawnBullet(localPlayer.x, localPlayer.y, localPlayer.aimAngle, myId, true);
  spawnParticles(
    localPlayer.x + Math.cos(localPlayer.aimAngle) * 16,
    localPlayer.y + Math.sin(localPlayer.aimAngle) * 16,
    '#ffffaa', 3
  );

  // Tell peers
  broadcast({ type: 'shoot', x: localPlayer.x, y: localPlayer.y, a: localPlayer.aimAngle, id: myId });

  if (w.ammo <= 0) startReload();
}

function startReload() {
  const w = localPlayer.weapon;
  if (w.reloading || w.reserve <= 0 || w.ammo >= 30) return;
  w.reloading = true;
  w.reloadStart = now();
}

function updateEnemies(dt) {
  if (!localPlayer) return;
  const allPlayers = Object.values(players).filter(p => !p.dead);

  for (const en of enemies) {
    if (en.health <= 0) continue;

    // Find nearest player
    let nearest = null, nearDist = Infinity;
    for (const p of allPlayers) {
      const d = Math.hypot(p.x - en.x, p.y - en.y);
      if (d < nearDist) { nearDist = d; nearest = p; }
    }
    if (!nearest) continue;

    const angle = Math.atan2(nearest.y - en.y, nearest.x - en.x);

    if (nearDist < 350) {
      en.state = 'chase';
    } else {
      en.state = 'patrol';
      en.patrolAngle += dt * 0.5;
    }

    const moveAngle = en.state === 'chase' ? angle : en.patrolAngle;
    const nx = en.x + Math.cos(moveAngle) * en.speed * dt;
    const ny = en.y + Math.sin(moveAngle) * en.speed * dt;
    if (!isSolid(nx, ny)) { en.x = nx; en.y = ny; }

    // Shooting (host only to avoid duplicate damage)
    if ((isHost || Object.keys(peers).length === 0) && nearDist < 280) {
      en.lastShotTime = (en.lastShotTime || 0) + dt * 1000;
      if (en.lastShotTime > en.fireRate) {
        en.lastShotTime = 0;
        // Slight inaccuracy
        const spread = 0.15;
        const shootAngle = angle + (Math.random() - 0.5) * spread;
        spawnBullet(en.x, en.y, shootAngle, en.id, false);
      }
    }
  }
}

function updateBullets(dt) {
  for (let i = bullets.length - 1; i >= 0; i--) {
    const b = bullets[i];
    b.x += b.vx * dt;
    b.y += b.vy * dt;
    b.life -= dt;

    if (b.life <= 0 || isSolid(b.x, b.y)) {
      spawnParticles(b.x, b.y, b.color, 4);
      bullets.splice(i, 1);
      continue;
    }

    // Cover blocks bullets
    if (isCover(b.x, b.y)) {
      spawnParticles(b.x, b.y, '#888', 3);
      bullets.splice(i, 1);
      continue;
    }

    // Hit enemies (player bullets only)
    if (b.ownerId === myId) {
      for (let j = enemies.length - 1; j >= 0; j--) {
        const en = enemies[j];
        if (en.health <= 0) continue;
        if (Math.hypot(b.x - en.x, b.y - en.y) < en.radius + b.radius) {
          en.health -= 20;
          spawnParticles(en.x, en.y, en.color, 10);
          bullets.splice(i, 1);
          if (en.health <= 0) {
            kills++;
            lootCollected += en.reward;
            addKillFeed(`YOU ▶ ${en.type.toUpperCase()}`);
            spawnParticles(en.x, en.y, en.color, 20);
            // Drop loot
            lootItems.push({
              type: 'valuables', icon: '💰', value: en.reward,
              color: '#ffd700', label: `+${en.reward} LOOT`,
              x: en.x, y: en.y,
              id: 'el_' + en.id, collected: false
            });
            broadcast({ type: 'enemy_dead', id: en.id });
          }
          break;
        }
      }
    }

    // Enemy bullets hit local player
    if (b.ownerId.startsWith('e_') && localPlayer && !localPlayer.dead) {
      if (Math.hypot(b.x - localPlayer.x, b.y - localPlayer.y) < localPlayer.radius + b.radius) {
        const en = enemies.find(e => e.id === b.ownerId);
        const dmg = en ? en.damage : 15;
        localPlayer.health -= dmg;
        showHit();
        spawnParticles(localPlayer.x, localPlayer.y, '#ff2a2a', 6);
        bullets.splice(i, 1);
        broadcast({ type: 'hit', target: myId, killer: b.ownerId, dmg });
        if (localPlayer.health <= 0) playerDead('ENEMY');
      }
    }

    // Bullet hits remote player (only locally for responsiveness, host authoritative in real game)
    if (b.ownerId === myId) {
      Object.values(players).forEach(p => {
        if (p.isLocal || p.dead) return;
        if (Math.hypot(b.x - p.x, b.y - p.y) < p.radius + b.radius) {
          broadcast({ type: 'hit', target: p.id, killer: myId, dmg: 20 });
        }
      });
    }
  }
}

function collectLoot(loot) {
  loot.collected = true;
  if (loot.type === 'ammo') {
    localPlayer.weapon.reserve = Math.min(localPlayer.weapon.reserve + loot.value, 180);
  } else if (loot.type === 'health') {
    localPlayer.health = Math.min(localPlayer.health + loot.value, MAX_HEALTH);
  } else {
    lootCollected += loot.value;
  }
  showLootPopup(loot.label);
  spawnParticles(loot.x, loot.y, loot.color, 12);
  broadcast({ type: 'loot_taken', lootId: loot.id });
}

let lootPopupTimer = null;
function showLootPopup(text) {
  const el = document.getElementById('lootPopup');
  el.textContent = text;
  el.style.opacity = '1';
  if (lootPopupTimer) clearTimeout(lootPopupTimer);
  lootPopupTimer = setTimeout(() => el.style.opacity = '0', 1500);
}

function showHit() {
  canvas.style.filter = 'brightness(2) saturate(0)';
  setTimeout(() => canvas.style.filter = '', 80);
}

let killFeedItems = [];
function addKillFeed(text) {
  const el = document.getElementById('killfeed');
  const entry = document.createElement('div');
  entry.className = 'kill-entry';
  entry.textContent = text;
  el.prepend(entry);
  killFeedItems.push(entry);
  if (killFeedItems.length > 5) {
    killFeedItems[0].remove();
    killFeedItems.shift();
  }
  setTimeout(() => { try { entry.remove(); } catch(e){} }, 4000);
}

// ─── Draw ────────────────────────────────────────────────
// Tile colors
const tileColors = {
  0: '#0a1520', // floor
  1: '#1a2535', // wall
  2: '#162030', // cover
  3: '#061520', // water
  4: '#0a1f10'  // extraction
};

const wallTopColors = {
  1: '#233045',
  2: '#1e3050'
};

function draw() {
  resizeCanvas();
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  ctx.save();
  ctx.translate(-cam.x, -cam.y);

  drawMap();
  drawExtractionZone();
  drawLoot();
  drawEnemies();
  drawBullets();
  drawParticles();
  drawPlayers();

  ctx.restore();

  drawMinimap();
}

function resizeCanvas() {
  if (canvas.width !== window.innerWidth || canvas.height !== window.innerHeight) {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
}
window.addEventListener('resize', resizeCanvas);

function drawMap() {
  const startX = Math.max(0, Math.floor(cam.x / TILE) - 1);
  const startY = Math.max(0, Math.floor(cam.y / TILE) - 1);
  const endX = Math.min(MAP_W, startX + Math.ceil(canvas.width / TILE) + 2);
  const endY = Math.min(MAP_H, startY + Math.ceil(canvas.height / TILE) + 2);

  for (let ty = startY; ty < endY; ty++) {
    for (let tx = startX; tx < endX; tx++) {
      const t = tileMap[ty][tx];
      const px = tx * TILE, py = ty * TILE;
      ctx.fillStyle = tileColors[t] || tileColors[0];
      ctx.fillRect(px, py, TILE, TILE);

      // Wall top face
      if (t === 1) {
        ctx.fillStyle = wallTopColors[1];
        ctx.fillRect(px, py, TILE, 6);
        // Outline
        ctx.strokeStyle = 'rgba(50,80,120,0.4)';
        ctx.lineWidth = 1;
        ctx.strokeRect(px + 0.5, py + 0.5, TILE-1, TILE-1);
      }
      if (t === 2) {
        // Cover crate
        ctx.fillStyle = '#1a3040';
        ctx.fillRect(px+4, py+4, TILE-8, TILE-8);
        ctx.strokeStyle = 'rgba(0,180,200,0.4)';
        ctx.lineWidth = 1;
        ctx.strokeRect(px+4, py+4, TILE-8, TILE-8);
        ctx.fillStyle = '#2a4560';
        ctx.fillRect(px+4, py+4, TILE-8, 5);
      }
      if (t === 4) {
        // Extraction zone ground
        const grd = ctx.createLinearGradient(px, py, px+TILE, py+TILE);
        grd.addColorStop(0, 'rgba(0,80,30,0.5)');
        grd.addColorStop(1, 'rgba(0,200,80,0.1)');
        ctx.fillStyle = grd;
        ctx.fillRect(px, py, TILE, TILE);
      }
      if (t === 3) {
        // Water shimmer
        ctx.fillStyle = `rgba(0,50,120,${0.4 + Math.sin(Date.now()*0.002 + tx*0.3 + ty*0.5)*0.1})`;
        ctx.fillRect(px, py, TILE, TILE);
      }

      // Grid lines (very subtle)
      if (t === 0 || t === 4) {
        ctx.strokeStyle = 'rgba(0,100,150,0.06)';
        ctx.lineWidth = 0.5;
        ctx.strokeRect(px, py, TILE, TILE);
      }
    }
  }
}

function drawExtractionZone() {
  const { x, y, r } = extractionZone;
  const t = Date.now() * 0.002;

  // Pulsing ring
  for (let i = 0; i < 3; i++) {
    const phase = (t + i * 0.7) % 1;
    ctx.beginPath();
    ctx.arc(x, y, r * (0.7 + phase * 0.5), 0, Math.PI * 2);
    ctx.strokeStyle = `rgba(0,255,100,${0.5 * (1 - phase)})`;
    ctx.lineWidth = 2;
    ctx.stroke();
  }

  ctx.beginPath();
  ctx.arc(x, y, r, 0, Math.PI * 2);
  ctx.strokeStyle = 'rgba(0,255,100,0.7)';
  ctx.lineWidth = 2;
  ctx.stroke();

  // Label
  ctx.fillStyle = 'rgba(0,255,100,0.8)';
  ctx.font = 'bold 11px Share Tech Mono';
  ctx.textAlign = 'center';
  ctx.fillText('EXTRACTION', x, y - r - 10);

  // Extraction progress arc
  if (isExtracting && extractionProgress > 0) {
    const pct = extractionProgress / EXTRACTION_TIME;
    ctx.beginPath();
    ctx.arc(x, y, r + 8, -Math.PI/2, -Math.PI/2 + Math.PI * 2 * pct);
    ctx.strokeStyle = '#ffd700';
    ctx.lineWidth = 4;
    ctx.stroke();
  }
}

function drawLoot() {
  for (const loot of lootItems) {
    if (loot.collected) continue;
    const { x, y, icon, color } = loot;
    const bob = Math.sin(Date.now() * 0.003 + x) * 3;

    // Glow
    ctx.shadowColor = color;
    ctx.shadowBlur = 10;
    ctx.beginPath();
    ctx.arc(x, y + bob, 10, 0, Math.PI * 2);
    ctx.fillStyle = color + '44';
    ctx.fill();
    ctx.shadowBlur = 0;

    // Icon
    ctx.font = '16px serif';
    ctx.textAlign = 'center';
    ctx.fillText(icon, x, y + bob + 6);
  }
}

function drawEnemies() {
  for (const en of enemies) {
    if (en.health <= 0) continue;
    const { x, y, radius, color, health, maxHealth } = en;

    // Shadow
    ctx.beginPath();
    ctx.ellipse(x, y + radius * 0.8, radius * 0.7, radius * 0.3, 0, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(0,0,0,0.4)';
    ctx.fill();

    // Body
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, Math.PI * 2);
    ctx.fillStyle = color;
    ctx.fill();
    ctx.strokeStyle = 'rgba(0,0,0,0.5)';
    ctx.lineWidth = 2;
    ctx.stroke();

    // Eye direction (toward nearest player)
    if (localPlayer) {
      const a = Math.atan2(localPlayer.y - y, localPlayer.x - x);
      ctx.beginPath();
      ctx.arc(x + Math.cos(a) * radius * 0.4, y + Math.sin(a) * radius * 0.4, 3, 0, Math.PI * 2);
      ctx.fillStyle = '#ff0';
      ctx.fill();
    }

    // Health bar
    if (health < maxHealth) {
      const bw = radius * 2;
      ctx.fillStyle = 'rgba(0,0,0,0.6)';
      ctx.fillRect(x - bw/2, y - radius - 8, bw, 4);
      ctx.fillStyle = health > maxHealth/2 ? '#00ff88' : '#ff2a2a';
      ctx.fillRect(x - bw/2, y - radius - 8, bw * (health/maxHealth), 4);
    }

    // Heavy indicator
    if (en.type === 'heavy') {
      ctx.beginPath();
      ctx.arc(x, y, radius + 4, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(255,100,30,0.4)';
      ctx.lineWidth = 2;
      ctx.stroke();
    }
    if (en.type === 'sniper') {
      ctx.beginPath();
      ctx.arc(x, y, radius + 4, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(170,0,255,0.5)';
      ctx.lineWidth = 1;
      ctx.setLineDash([3, 3]);
      ctx.stroke();
      ctx.setLineDash([]);
    }
  }
}

function drawBullets() {
  for (const b of bullets) {
    ctx.beginPath();
    ctx.arc(b.x, b.y, b.radius, 0, Math.PI * 2);
    ctx.fillStyle = b.color;
    ctx.shadowColor = b.color;
    ctx.shadowBlur = 8;
    ctx.fill();
    ctx.shadowBlur = 0;

    // Tracer
    ctx.beginPath();
    ctx.moveTo(b.x, b.y);
    ctx.lineTo(b.x - b.vx * 0.025, b.y - b.vy * 0.025);
    ctx.strokeStyle = b.color + '88';
    ctx.lineWidth = 2;
    ctx.stroke();
  }
}

function drawParticles() {
  for (const p of particles) {
    const a = p.life / (p.maxLife || 0.8);
    ctx.beginPath();
    ctx.arc(p.x, p.y, p.radius * a, 0, Math.PI * 2);
    ctx.fillStyle = p.color + Math.floor(a * 255).toString(16).padStart(2, '0');
    ctx.fill();
  }
}

function drawPlayers() {
  Object.values(players).forEach(p => drawPlayer(p));
}

function drawPlayer(p) {
  if (p.dead) return;
  const { x, y, radius, color, aimAngle, health, maxHealth, name, isLocal } = p;

  // Shadow
  ctx.beginPath();
  ctx.ellipse(x, y + radius * 0.8, radius * 0.7, radius * 0.3, 0, 0, Math.PI * 2);
  ctx.fillStyle = 'rgba(0,0,0,0.4)';
  ctx.fill();

  // Outline glow
  if (isLocal) {
    ctx.shadowColor = color;
    ctx.shadowBlur = 15;
  }

  // Body
  ctx.beginPath();
  ctx.arc(x, y, radius, 0, Math.PI * 2);
  ctx.fillStyle = isLocal ? '#0a1520' : '#1a0a00';
  ctx.fill();
  ctx.strokeStyle = color;
  ctx.lineWidth = isLocal ? 2.5 : 2;
  ctx.stroke();
  ctx.shadowBlur = 0;

  // Crosshair direction
  const gunLen = radius + 12;
  ctx.beginPath();
  ctx.moveTo(x + Math.cos(aimAngle) * radius, y + Math.sin(aimAngle) * radius);
  ctx.lineTo(x + Math.cos(aimAngle) * gunLen, y + Math.sin(aimAngle) * gunLen);
  ctx.strokeStyle = color;
  ctx.lineWidth = 3;
  ctx.stroke();

  // Gun barrel end dot
  ctx.beginPath();
  ctx.arc(x + Math.cos(aimAngle) * gunLen, y + Math.sin(aimAngle) * gunLen, 3, 0, Math.PI * 2);
  ctx.fillStyle = color;
  ctx.fill();

  // Player icon center
  ctx.font = 'bold 11px Orbitron';
  ctx.textAlign = 'center';
  ctx.fillStyle = color;
  ctx.fillText('+', x, y + 4);

  // Name
  ctx.font = '10px Share Tech Mono';
  ctx.fillStyle = isLocal ? 'rgba(0,255,231,0.8)' : 'rgba(255,100,50,0.8)';
  ctx.fillText(name, x, y - radius - 6);

  // Health bar
  const bw = radius * 2.5;
  ctx.fillStyle = 'rgba(0,0,0,0.6)';
  ctx.fillRect(x - bw/2, y - radius - 4, bw, 3);
  ctx.fillStyle = isLocal ? (health > 30 ? '#00ff88' : '#ff2a2a') : '#ff6b35';
  ctx.fillRect(x - bw/2, y - radius - 4, bw * (health/maxHealth), 3);

  // Reload indicator
  if (isLocal && p.weapon?.reloading) {
    const pct = (now() - p.weapon.reloadStart) / p.weapon.reloadTime;
    ctx.beginPath();
    ctx.arc(x, y, radius + 7, -Math.PI/2, -Math.PI/2 + Math.PI * 2 * pct);
    ctx.strokeStyle = '#ffd700';
    ctx.lineWidth = 3;
    ctx.stroke();
  }
}

// ─── Minimap ─────────────────────────────────────────────
function drawMinimap() {
  minimapCtx.clearRect(0, 0, 110, 110);
  minimapCtx.fillStyle = 'rgba(0,5,10,0.85)';
  minimapCtx.fillRect(0, 0, 110, 110);

  const scaleX = 110 / (MAP_W * TILE);
  const scaleY = 110 / (MAP_H * TILE);

  // Tiles
  for (let ty = 0; ty < MAP_H; ty++) {
    for (let tx = 0; tx < MAP_W; tx++) {
      const t = tileMap[ty][tx];
      if (t === 1) {
        minimapCtx.fillStyle = '#2a3a50';
        minimapCtx.fillRect(tx * TILE * scaleX, ty * TILE * scaleY, TILE*scaleX+0.5, TILE*scaleY+0.5);
      }
      if (t === 4) {
        minimapCtx.fillStyle = 'rgba(0,200,80,0.4)';
        minimapCtx.fillRect(tx * TILE * scaleX, ty * TILE * scaleY, TILE*scaleX+0.5, TILE*scaleY+0.5);
      }
    }
  }

  // Enemies
  for (const en of enemies) {
    if (en.health <= 0) continue;
    minimapCtx.beginPath();
    minimapCtx.arc(en.x * scaleX, en.y * scaleY, 1.5, 0, Math.PI * 2);
    minimapCtx.fillStyle = '#ff2a2a';
    minimapCtx.fill();
  }

  // Loot
  for (const l of lootItems) {
    if (l.collected) continue;
    minimapCtx.beginPath();
    minimapCtx.arc(l.x * scaleX, l.y * scaleY, 1, 0, Math.PI * 2);
    minimapCtx.fillStyle = '#ffd700';
    minimapCtx.fill();
  }

  // Other players
  Object.values(players).filter(p => !p.isLocal && !p.dead).forEach(p => {
    minimapCtx.beginPath();
    minimapCtx.arc(p.x * scaleX, p.y * scaleY, 2.5, 0, Math.PI * 2);
    minimapCtx.fillStyle = '#ff6b35';
    minimapCtx.fill();
  });

  // Local player
  if (localPlayer) {
    minimapCtx.beginPath();
    minimapCtx.arc(localPlayer.x * scaleX, localPlayer.y * scaleY, 3, 0, Math.PI * 2);
    minimapCtx.fillStyle = '#00ffe7';
    minimapCtx.fill();
  }

  // Camera viewport box
  minimapCtx.strokeStyle = 'rgba(0,255,231,0.3)';
  minimapCtx.lineWidth = 0.5;
  minimapCtx.strokeRect(
    cam.x * scaleX, cam.y * scaleY,
    canvas.width * scaleX, canvas.height * scaleY
  );
}

// ─── HUD ─────────────────────────────────────────────────
function updateHUD() {
  if (!localPlayer) return;
  const h = localPlayer.health;
  document.getElementById('healthVal').textContent = Math.max(0, Math.floor(h));
  document.getElementById('healthFill').style.width = Math.max(0, h/MAX_HEALTH * 100) + '%';
  document.getElementById('lootVal').textContent = lootCollected;
  document.getElementById('killCount').textContent = kills;
  const w = localPlayer.weapon;
  const reload = w.reloading ? ' [R]' : '';
  document.getElementById('ammoDisplay').textContent = `${w.ammo} / ${w.reserve}${reload}`;
}

function updateZoneHUD() {
  const min = Math.floor(gameTime / 60).toString().padStart(2, '0');
  const sec = (gameTime % 60).toString().padStart(2, '0');
  document.getElementById('zoneTimer').textContent = `${min}:${sec}`;
  if (gameTime <= 30) {
    document.getElementById('zoneTimer').style.color = '#ff2a2a';
  }
}

function setConnStatus(color, text) {
  const dot = document.getElementById('connDot');
  dot.className = 'dot ' + color;
  document.getElementById('connText').textContent = text;
}

// ─── End States ──────────────────────────────────────────
function playerDead(killer) {
  if (gameState !== 'playing') return;
  gameState = 'dead';
  localPlayer.dead = true;
  clearInterval(gameTimer);
  saveRun(false);

  document.getElementById('gameOverScreen').style.display = 'flex';
  document.getElementById('goTitle').textContent = 'ELIMINATED';
  document.getElementById('goTitle').className = 'go-title dead';
  document.getElementById('goStats').textContent =
    `KILLS: ${kills}  |  LOOT: ${lootCollected}  |  KILLED BY: ${killer}`;

  setTimeout(() => {
    document.getElementById('hud').style.display = 'none';
    document.getElementById('minimap').style.display = 'none';
    document.getElementById('joystickLeft').style.display = 'none';
    document.getElementById('joystickRight').style.display = 'none';
  }, 200);
}

function playerExtracted() {
  if (gameState !== 'playing') return;
  gameState = 'won';
  clearInterval(gameTimer);
  saveRun(true);

  document.getElementById('gameOverScreen').style.display = 'flex';
  document.getElementById('goTitle').textContent = 'EXTRACTED';
  document.getElementById('goTitle').className = 'go-title win';
  document.getElementById('goStats').textContent =
    `KILLS: ${kills}  |  LOOT: ${lootCollected}  |  TIME SURVIVED: ${ZONE_CLOSE_TIME - gameTime}s`;

  setTimeout(() => {
    document.getElementById('hud').style.display = 'none';
    document.getElementById('minimap').style.display = 'none';
    document.getElementById('joystickLeft').style.display = 'none';
    document.getElementById('joystickRight').style.display = 'none';
  }, 200);
}

async function saveRun(success) {
  const run = {
    date: new Date().toISOString(),
    success, kills, loot: lootCollected,
    timeSurvived: ZONE_CLOSE_TIME - gameTime
  };
  dbSet('runs', run);

  // Update profile
  let profile = await dbGet('profile', 'main') || {
    id: 'main', totalRuns: 0, totalKills: 0, totalLoot: 0, extractions: 0
  };
  profile.totalRuns++;
  profile.totalKills += kills;
  profile.totalLoot += lootCollected;
  if (success) profile.extractions++;
  dbSet('profile', profile);
}

// ─── Init ────────────────────────────────────────────────
window.addEventListener('load', () => {
  resizeCanvas();
  initDB().then(() => {
    console.log('EXFIL // ZERO ready');
  });
});

// Prevent context menu on long press
document.addEventListener('contextmenu', e => e.preventDefault());
</script>
</body>
</html>
