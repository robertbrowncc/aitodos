<template>
  <!-- Empty template, component only handles keyboard events -->
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const KONAMI_CODE = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
let currentIndex = 0;

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

const triggerEasterEgg = async () => {
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

    // Call the database reset endpoint
    const response = await axios.post('/api/reset-database');
    console.log('Database reset response:', response.data);
    
    if (response.data.status === 'success') {
      // Show a success message
      const message = document.createElement('div');
      message.textContent = '🎮 Database Reset Complete! 🎮';
      message.style.position = 'fixed';
      message.style.top = '20px';
      message.style.left = '50%';
      message.style.transform = 'translateX(-50%)';
      message.style.backgroundColor = '#4CAF50';
      message.style.color = 'white';
      message.style.padding = '10px 20px';
      message.style.borderRadius = '5px';
      message.style.zIndex = '9999';
      document.body.appendChild(message);
      
      setTimeout(() => {
        document.body.removeChild(message);
        // Force reload from server, not cache
        window.location.href = window.location.href + '?t=' + new Date().getTime();
      }, 3000);
    } else {
      throw new Error(response.data.migrate_output || 'Database reset failed');
    }
  } catch (error) {
    console.error('Failed to reset database:', error);
    
    // Show an error message
    const message = document.createElement('div');
    message.textContent = '❌ Database Reset Failed! ❌';
    message.style.position = 'fixed';
    message.style.top = '20px';
    message.style.left = '50%';
    message.style.transform = 'translateX(-50%)';
    message.style.backgroundColor = '#f44336';
    message.style.color = 'white';
    message.style.padding = '10px 20px';
    message.style.borderRadius = '5px';
    message.style.zIndex = '9999';
    document.body.appendChild(message);
    
    setTimeout(() => {
      document.body.removeChild(message);
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
    transform: translateY(-100vh) scale(0);
    opacity: 1;
  }
  100% {
    transform: translateY(100vh) scale(1);
    opacity: 0;
  }
}
</style>
