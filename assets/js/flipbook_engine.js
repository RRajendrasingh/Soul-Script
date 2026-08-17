/**
 * SoulScript - 3D Interactive Virtual FlipBook Engine
 * Built on StPageFlip Canvas/WebGL with Web Audio API Synthesized Paper Turn Sound FX
 */

class SoulScriptFlipbook {
  constructor(options = {}) {
    this.containerId = options.containerId || 'soulscriptFlipbook';
    this.modalId = options.modalId || 'soulscriptFlipbookModal';
    this.pageFlip = null;
    this.soundEnabled = true;
    this.audioCtx = null;
    this.currentPage = 0;
    this.totalPages = 0;
    this.onPageChange = options.onPageChange || null;
  }

  // Synthesize realistic paper flip sound via Web Audio API (Zero external MP3 lag)
  playPaperFlipSound() {
    if (!this.soundEnabled) return;
    try {
      if (!this.audioCtx) {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) this.audioCtx = new AudioContext();
      }
      if (this.audioCtx && this.audioCtx.state === 'suspended') {
        this.audioCtx.resume();
      }
      if (!this.audioCtx) return;

      const bufferSize = this.audioCtx.sampleRate * 0.12; // 120ms gentle rustle
      const buffer = this.audioCtx.createBuffer(1, bufferSize, this.audioCtx.sampleRate);
      const output = buffer.getChannelData(0);
      for (let i = 0; i < bufferSize; i++) {
        output[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.35));
      }

      const whiteNoise = this.audioCtx.createBufferSource();
      whiteNoise.buffer = buffer;

      const filter = this.audioCtx.createBiquadFilter();
      filter.type = 'bandpass';
      filter.frequency.setValueAtTime(800, this.audioCtx.currentTime);
      filter.frequency.exponentialRampToValueAtTime(3200, this.audioCtx.currentTime + 0.08);
      filter.Q.value = 1.2;

      const gainNode = this.audioCtx.createGain();
      gainNode.gain.setValueAtTime(0.28, this.audioCtx.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + 0.12);

      whiteNoise.connect(filter);
      filter.connect(gainNode);
      gainNode.connect(this.audioCtx.destination);

      whiteNoise.start();
    } catch (e) {
      // Audio not supported or blocked by browser policy
    }
  }

  init(containerEl) {
    const el = containerEl || document.getElementById(this.containerId);
    if (!el || typeof St === 'undefined' || !St.PageFlip) return null;

    // Destroy existing instance if any
    if (this.pageFlip) {
      try { this.pageFlip.destroy(); } catch (e) {}
    }

    const isMobile = window.innerWidth < 768;
    const bookWidth = isMobile ? Math.min(window.innerWidth - 32, 380) : 480;
    const bookHeight = isMobile ? Math.round(bookWidth * 1.42) : 640;

    this.pageFlip = new St.PageFlip(el, {
      width: bookWidth,
      height: bookHeight,
      size: 'stretch',
      minWidth: 280,
      maxWidth: 600,
      minHeight: 400,
      maxHeight: 800,
      maxShadowOpacity: 0.55,
      showCover: true,
      mobileScrollSupport: true,
      swipeDistance: 30,
      usePortrait: true,
      startPage: 0,
      flippingTime: 700,
      drawShadow: true,
      autoSize: true
    });

    const pages = el.querySelectorAll('.fb-page');
    this.totalPages = pages.length;
    this.pageFlip.loadFromHTML(pages);

    this.pageFlip.on('flip', (e) => {
      this.currentPage = e.data;
      this.playPaperFlipSound();
      this.updateCounter();
      if (this.onPageChange) this.onPageChange(this.currentPage, this.totalPages);
    });

    this.updateCounter();
    return this.pageFlip;
  }

  updateCounter() {
    const counterEl = document.getElementById('fbPageCounter');
    if (counterEl && this.pageFlip) {
      const current = (this.pageFlip.getCurrentPageIndex() || 0) + 1;
      counterEl.textContent = `${current} / ${this.totalPages}`;
    }
  }

  flipNext() {
    if (this.pageFlip) this.pageFlip.flipNext();
  }

  flipPrev() {
    if (this.pageFlip) this.pageFlip.flipPrev();
  }

  toggleSound() {
    this.soundEnabled = !this.soundEnabled;
    const soundBtn = document.getElementById('fbSoundToggleBtn');
    if (soundBtn) {
      soundBtn.innerHTML = this.soundEnabled 
        ? '<i data-lucide="volume-2" class="w-4 h-4 text-[#eac34a]"></i>' 
        : '<i data-lucide="volume-x" class="w-4 h-4 text-gray-400"></i>';
      if (typeof lucide !== 'undefined') lucide.createIcons();
    }
  }

  openModal() {
    const modal = document.getElementById(this.modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    setTimeout(() => {
      const container = document.getElementById(this.containerId);
      if (container) this.init(container);
    }, 100);
  }

  closeModal() {
    const modal = document.getElementById(this.modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  }
}

window.SoulScriptFlipbook = SoulScriptFlipbook;
