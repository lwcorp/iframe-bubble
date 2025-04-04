<?php
/**
 * Enqueue inline styles and JavaScript for the chat functionality.
 */
function my_chat_enqueue_assets() {
    // Register and enqueue an empty CSS handle to which we attach our inline styles.
    wp_register_style('my-chat-style', false);
    wp_enqueue_style('my-chat-style');
    $chat_css = "
      .chat-balloon {
        position: fixed;
        bottom: 20px;
        width: 60px;
        height: 60px;
        cursor: pointer;
        z-index: 1000;
      }
      .chat-balloon svg {
        width: 100%;
        height: auto;
        border-radius: 50%;
      }
      .chat-balloon.he {
        right: 20px;
      }
      .chat-balloon.en {
        left: 20px;
      }
      
      .chat-modal {
        position: fixed;
        bottom: 20px;
        background: #fff;
        z-index: 1001;
        border-radius: 5px;
        overflow: visible;
        opacity: 0;
        pointer-events: none;
        transform: translateY(100%);
        transition: transform 0.5s ease, opacity 0.5s ease;
      }
      .chat-modal.active {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
      }
      .chat-modal.he {
        right: 20px;
        box-shadow: -2px 0 4px rgba(0, 0, 0, 0.3);
      }
      .chat-modal.en {
        left: 20px;
        box-shadow: 2px 0 4px rgba(0, 0, 0, 0.3);
      }
      
      .chat-modal-close {
        position: absolute;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 50%;
        font-size: 1.5em;
        cursor: pointer;
        color: #333;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        z-index: 1002;
      }
      .chat-modal-close.he {
        top: -15px;
        left: -25px;
      }
      .chat-modal-close.en {
        top: -25px;
        right: -25px;
      }
      @media (prefers-color-scheme: dark) {
        .chat-modal-close {
          background: #333;
          border: 1px solid #404040;
          color: #fff;
        }
      }
    ";
    wp_add_inline_style('my-chat-style', $chat_css);

    // Register and enqueue an empty JS handle to which we attach our inline JavaScript.
    wp_register_script('my-chat-script', false, [], false, true);
    wp_enqueue_script('my-chat-script');
    $chat_js = "
    document.addEventListener('DOMContentLoaded', genChatballon());
	function genChatballon() {
	  // You can change the following - Start
      // Allow opening multiple bubbles simultaneously
      var dualchat_default = false;
      // Added chatlang parameter to control which chat bubbles appear
      var chatlang_default = 'en,he'; // Can be empty string, all, or comma-separated languages like en,he
	  // You can change the following - End
	  
	  // Determine settings
	  const dualchat = document.forms[0]?.dualchat?.checked || dualchat_default;
	  const chatlang = document.forms[0]?.chatlang?.value || chatlang_default;

      // Hebrew Chat listeners
      var chatBalloon = document.querySelector('.chat-balloon.he');
      var chatModal = document.querySelector('.chat-modal.he');
      var chatModalClose = document.querySelector('.chat-modal.he .chat-modal-close');
      var iframeContainer = document.getElementById('iframe-container');
	  const iframeMarkup = document.forms[0]?.iframe_rtl?.value || document.getElementById('iframe-markup').value.trim();
    
      // English Chat listeners
      var chatBalloonEn = document.querySelector('.chat-balloon.en');
      var chatModalEn = document.querySelector('.chat-modal.en');
      var chatModalCloseEn = document.querySelector('.chat-modal.en .chat-modal-close');
      var iframeContainerEn = document.getElementById('iframe-container-en');
	  const iframeMarkupEn = document.forms[0]?.iframe_ltr?.value || document.getElementById('iframe-markup-en').value.trim();
      
      // Function to control chat bubble visibility based on chatlang
      function updateChatBubbleVisibility() {
        // Hide all chat bubbles by default
        chatBalloon.style.display = 'none';
        chatBalloonEn.style.display = 'none';
        
        // If chatlang is empty, no bubbles should appear
        if (!chatlang) {
          return;
        }
        
        // If chatlang is 'all', show both bubbles
        if (chatlang === 'all') {
          chatBalloon.style.display = 'block';
          chatBalloonEn.style.display = 'block';
          return;
        }
        
        var languages = chatlang.replace(/\s+/g, '').split(',');
        
        if (languages.includes('he')) {
          chatBalloon.style.display = 'block';
        }
        
        if (languages.includes('en')) {
          chatBalloonEn.style.display = 'block';
        }
      }
      
      // Initialize chat bubble visibility
      updateChatBubbleVisibility();
	  
 	  chatBalloon.removeEventListener('click', parent.iframeOpenWrapper);
	  chatBalloonEn.removeEventListener('click', parent.iframeOpenWrapperEn);
	  chatModalClose.removeEventListener('click', parent.iframeCloseWrapper);
	  chatModalCloseEn.removeEventListener('click', parent.iframeCloseWrapperEn);

      parent.iframeOpenWrapper = function(e) {
	    iframeOpen(iframeContainer, iframeMarkup, chatModal, dualchat, '.chat-modal.en');
	  }
      parent.iframeOpenWrapperEn = function(e) {
  		iframeOpen(iframeContainerEn, iframeMarkupEn, chatModalEn, dualchat, '.chat-modal.he');
	  }
      parent.iframeCloseWrapper = function(e) {
		iframeClose(e, chatModal);
	  }
      parent.iframeCloseWrapperEn = function(e) {
	    iframeClose(e, chatModalEn);
	  }
	  
	  chatBalloon.addEventListener('click', parent.iframeOpenWrapper);
	  chatBalloonEn.addEventListener('click', parent.iframeOpenWrapperEn);
	  chatModalClose.addEventListener('click', parent.iframeCloseWrapper);
	  chatModalCloseEn.addEventListener('click', parent.iframeCloseWrapperEn);
	  
	  function iframeOpen(container, markup, modal, dualchat, elm) {
        container.innerHTML = markup;
        // If dualchat is false, close the previous chat modal before opening another chat one
        if (!dualchat) {
          document.querySelector(elm).classList.remove('active');
        }
        modal.classList.add('active');
      }
	  
	  function iframeClose(evt, elm) {
        evt.preventDefault();
        elm.classList.remove('active');
      }
    }
    ";
    wp_add_inline_script('my-chat-script', $chat_js);
}

/**
 * Outputs the chat HTML markup in the footer.
 */
function my_chat_markup() {
    echo <<<'EOT'
    <!-- Floating Chat Balloon for Hebrew Chat -->
    <div id="chat-balloon" class="chat-balloon he">
      <svg width="60" height="60" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <filter id="textShadow" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0.5" dy="0.5" stdDeviation="0.5" flood-color="#000" flood-opacity="0.3"></feDropShadow>
          </filter>
        </defs>
        <path fill="#ff9900" stroke="#ff6600" stroke-width="4" d="
          M8,8 
          h40 
          a4,4 0 0 1 4,4 
          v24 
          a4,4 0 0 1 -4,4 
          h-10 
          l-8,8 
          l-8,-8 
          h-10 
          a4,4 0 0 1 -4,-4 
          v-24 
          a4,4 0 0 1 4,-4 
          z"/>
        <text x="45%" y="48%" text-anchor="middle" fill="#003366" font-size="0.8em" font-weight="bold" font-family="'Arial Hebrew', 'Noto Sans Hebrew', sans-serif" filter="url(#textShadow)">HE</text>
      </svg>
    </div>
    
    <!-- Modal Overlay for the Hebrew iFrame -->
    <div id="chat-modal" class="chat-modal he">
      <span class="chat-modal-close he">&times;</span>
      <div id="chat-modal-content">
        <div id="iframe-container"></div>
        <textarea id="iframe-markup" style="display:none;">
          <iframe srcdoc="<style>body {direction: rtl;margin-top: 40%; margin-right: 40%;}</style>דוגמא ל-iframe" width="768px" height="710px" frameBorder="0" allowFullScreen></iframe>
        </textarea>
      </div>
    </div>
    
    <!-- Floating Chat Balloon for English Chat -->
    <div id="chat-balloon-en" class="chat-balloon en">
      <svg width="60" height="60" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <filter id="textShadowEn" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0.5" dy="0.5" stdDeviation="0.5" flood-color="#000" flood-opacity="0.3"/>
          </filter>
        </defs>
        <path fill="#ff9900" stroke="#ff6600" stroke-width="4" d="
          M8,8 
          h40 
          a4,4 0 0 1 4,4 
          v24 
          a4,4 0 0 1 -4,4 
          h-10 
          l-8,8 
          l-8,-8 
          h-10 
          a4,4 0 0 1 -4,-4 
          v-24 
          a4,4 0 0 1 4,-4 
          z"/>
        <text x="45%" y="48%" text-anchor="middle" fill="#003366" font-size="0.8em" font-weight="bold" font-family="Arial, Helvetica, sans-serif" filter="url(#textShadowEn)">EN</text>
      </svg>
    </div>
    
    <!-- Modal Overlay for the English iFrame -->
    <div id="chat-modal-en" class="chat-modal en">
      <span class="chat-modal-close en">&times;</span>
      <div id="chat-modal-content-en">
        <div id="iframe-container-en"></div>
        <textarea id="iframe-markup-en" style="display:none;">
          <iframe srcdoc="<style>body {margin-top: 40%; margin-left: 40%;}</style>This is a sample iframe" width="768px" height="710px" frameBorder="0" allowFullScreen></iframe>
        </textarea>
      </div>
    </div>
EOT;
}

// Hook the functions to the appropriate actions.
add_action('wp_enqueue_scripts', 'my_chat_enqueue_assets');
add_action('wp_footer', 'my_chat_markup');
?>
