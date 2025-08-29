<script setup>
import favicon from  '@/assets/vanti/icon.png';
</script>

<template>
  <div>
    <Head>
      <title>Vanti - Similtech</title>
      <link rel="icon" type="image/png" :href="favicon">
    </Head>

    <main>
      <slot/>
    </main>

  </div>
</template>
