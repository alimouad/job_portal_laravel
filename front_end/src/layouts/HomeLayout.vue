<script setup>
import Footer from '@/components/Footer.vue';
import NavBar from '@/components/NavBar.vue';



</script>

<template>
    <div  class="page-root min-h-screen bg-[#f5f5f5] text-[#141414]">

        <NavBar />

        <div class="w-full">
             <slot />
        </div>

        <Footer/>
       
            
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

div {
  font-family: 'Plus Jakarta Sans', sans-serif;
}
</style>