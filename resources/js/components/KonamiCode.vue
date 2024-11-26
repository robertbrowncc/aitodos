<template>
  <!-- Empty template, component only handles keyboard events -->
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['easter-egg-success', 'error']);

const KONAMI_CODE = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
let currentIndex = 0;
const message = ref('');
const isResetting = ref(false);
let activeMessageDiv = null;

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
  // Remove any existing message
  if (activeMessageDiv && document.body.contains(activeMessageDiv)) {
    document.body.removeChild(activeMessageDiv);
  }

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
  
  activeMessageDiv = messageDiv;
  return messageDiv;
};

const removeMessage = (messageDiv) => {
  if (messageDiv && document.body.contains(messageDiv)) {
    document.body.removeChild(messageDiv);
  }
  if (activeMessageDiv === messageDiv) {
    activeMessageDiv = null;
  }
};

const triggerEasterEgg = async () => {
  let messageDiv = null;
  try {
    // Show loading message
    messageDiv = showMessage('🎮 Resetting Database... 🎮');
    await resetDatabase();
  } catch (error) {
    emit('error', 'Failed to trigger easter egg: ' + error.message);
    
    // Show error message
    if (messageDiv) {
      messageDiv.textContent = '❌ Database Reset Failed! ❌';
      messageDiv.style.backgroundColor = '#f44336';
    } else {
      messageDiv = showMessage('❌ Database Reset Failed! ❌', true);
    }
    
    // Remove error message after delay
    setTimeout(() => removeMessage(messageDiv), 5000);
  }
};

const resetDatabase = async () => {
  if (isResetting.value) return;
  
  isResetting.value = true;
  message.value = 'Resetting database...';
  
  try {
    const response = await axios.post('/api/reset-database');
    emit('easter-egg-success');
    message.value = 'Database reset successful! Reloading...';
    
    // Show success message before reload
    showMessage('🎉 Database reset successful! Reloading... 🎉');
    
    setTimeout(() => {
      window.location.reload();
    }, 1500);
  } catch (error) {
    console.error('Failed to reset database:', error);
    emit('error', 'Failed to reset database: ' + error.message);
    message.value = 'Failed to reset database. Please try again.';
    throw error; // Re-throw to be handled by triggerEasterEgg
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
  // Clean up any remaining message
  if (activeMessageDiv) {
    removeMessage(activeMessageDiv);
  }
});
</script>

<style scoped>
</style>
