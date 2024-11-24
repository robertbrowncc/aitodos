<template>
  <!-- Empty template, component only handles keyboard events -->
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import axios from 'axios';

const KONAMI_CODE = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
let currentIndex = 0;
const message = ref('');
const isResetting = ref(false);

const handleKeydown = (event) => {
  // Check if the pressed key matches the next key in the sequence
  if (event.key === KONAMI_CODE[currentIndex]) {
    currentIndex++;
    
    // If we've matched all keys, trigger the easter egg
    if (currentIndex === KONAMI_CODE.length) {
      triggerEasterEgg();
      currentIndex = 0; // Reset for next time
    }
  } else {
    currentIndex = 0; // Reset on any wrong key
    // If the wrong key is the first key of the sequence, start over
    if (event.key === KONAMI_CODE[0]) {
      currentIndex = 1;
    }
  }
};

const showMessage = (messageText, isError = false) => {
  const messageDiv = document.createElement('div');
  messageDiv.textContent = messageText;
  messageDiv.style.position = 'fixed';
  messageDiv.style.top = '20px';
  messageDiv.style.left = '50%';
  messageDiv.style.transform = 'translateX(-50%)';
  messageDiv.style.backgroundColor = isError ? '#f44336' : '#4CAF50';
  messageDiv.style.color = 'white';
  messageDiv.style.padding = '10px 20px';
  messageDiv.style.borderRadius = '5px';
  messageDiv.style.zIndex = '9999';
  messageDiv.style.textAlign = 'center';
  messageDiv.style.minWidth = '300px';
  messageDiv.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
  document.body.appendChild(messageDiv);
  
  return messageDiv;
};

const triggerEasterEgg = async () => {
  let messageDiv = null;
  try {
    // Create a fun animation effect
    document.body.style.transition = 'all 1s';
    document.body.style.transform = 'rotate(360deg)';
    
    // Add some sparkles or confetti effect
    const emojis = ['✨', '🌟', '⭐', '💫', '🎉'];
    for (let i = 0; i < 20; i++) {
      const emoji = document.createElement('div');
      emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
      emoji.style.position = 'fixed';
      emoji.style.left = Math.random() * 100 + 'vw';
      emoji.style.top = Math.random() * 100 + 'vh';
      emoji.style.animation = 'fall 3s forwards';
      document.body.appendChild(emoji);
      
      setTimeout(() => {
        document.body.removeChild(emoji);
      }, 3000);
    }

    // Reset the rotation after animation
    setTimeout(() => {
      document.body.style.transform = 'none';
    }, 1000);

    // Show loading message
    messageDiv = showMessage('🎮 Resetting Database... 🎮');

    await resetDatabase();
  } catch (error) {
    // Show error message
    if (messageDiv) {
      messageDiv.textContent = '❌ Database Reset Failed! Check console for details ❌';
      messageDiv.style.backgroundColor = '#f44336';
    } else {
      messageDiv = showMessage('❌ Database Reset Failed! Check console for details ❌', true);
    }
    
    // Remove error message after delay
    setTimeout(() => {
      document.body.removeChild(messageDiv);
    }, 5000);
  }
};

const resetDatabase = async () => {
  isResetting.value = true;
  message.value = 'Resetting database...';
  
  try {
    await axios.post('/api/reset-database');
    this.$emit('easter-egg-success');
    message.value = 'Database reset successful! Reloading...';
    setTimeout(() => {
      window.location.reload();
    }, 1500);
  } catch (error) {
    this.$emit('error', 'Failed to trigger easter egg: ' + error.message);
    message.value = 'Failed to reset database. Please try again.';
  } finally {
    setTimeout(() => {
      message.value = '';
      isResetting.value = false;
    }, 3000);
  }
};

// Add and remove event listener
onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
@keyframes fall {
  0% {
    transform: translateY(-100vh) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(100vh) rotate(360deg);
    opacity: 0;
  }
}
</style>
