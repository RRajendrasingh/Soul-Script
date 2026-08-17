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
    // Calculate dynamic available dimensions to fit 100% inside viewport without vertical scrollbars
    const availHeight = Math.max(window.innerHeight - 130, 360);
    const maxDesktopHalfWidth = Math.floor((window.innerWidth - 120) / 2);
    
    let bookWidth = isMobile ? Math.min(window.innerWidth - 32, 380) : Math.min(maxDesktopHalfWidth, 480);
    let bookHeight = Math.min(availHeight, Math.round(bookWidth * 1.38));
    
    // Recalculate width if height constrained
    if (bookHeight < availHeight * 0.85 && !isMobile) {
      bookWidth = Math.round(bookHeight / 1.38);
    }

    this.pageFlip = new St.PageFlip(el, {
      width: Math.max(bookWidth, 260),
      height: Math.max(bookHeight, 380),
      size: 'stretch',
      minWidth: 240,
      maxWidth: 550,
      minHeight: 350,
      maxHeight: 780,
      maxShadowOpacity: 0.5,
      showCover: true,
      mobileScrollSupport: false, // Prevent page drag from scrolling main window
      swipeDistance: 25,
      usePortrait: isMobile,
      startPage: 0,
      flippingTime: 600,
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
    this.attachWheelListener();
    return this.pageFlip;
  }

  // Mouse Wheel Scroll Page Flipping (Scroll Wheel -> Flip Pages)
  attachWheelListener() {
    const modal = document.getElementById(this.modalId);
    if (!modal || modal.__wheelAttached) return;
    modal.__wheelAttached = true;

    let isThrottled = false;
    modal.addEventListener('wheel', (e) => {
      e.preventDefault();
      if (isThrottled || !this.pageFlip) return;

      if (e.deltaY > 20) {
        this.flipNext();
        isThrottled = true;
        setTimeout(() => { isThrottled = false; }, 450);
      } else if (e.deltaY < -20) {
        this.flipPrev();
        isThrottled = true;
        setTimeout(() => { isThrottled = false; }, 450);
      }
    }, { passive: false });
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
        ? '<svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11 5L6 9H2v6h4l5 4V5zM15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14"/></svg>' 
        : '<svg class="w-4 h-4 fill-current opacity-40" viewBox="0 0 24 24"><path d="M11 5L6 9H2v6h4l5 4V5zM23 9l-6 6M17 9l6 6"/></svg>';
    }
  }

  openModal() {
    const modal = document.getElementById(this.modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.documentElement.style.overflow = 'hidden';
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
    modal.style.display = 'none';
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
  }
}

window.SoulScriptFlipbook = SoulScriptFlipbook;
