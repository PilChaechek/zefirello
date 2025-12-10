<script setup lang="ts">
import { Button } from '@/components/ui/button'
</script>

<template>
    <!--
      h-screen: на всю высоту
      bg-background: цвет фона из темы shadcn (проверка CSS переменных)
      text-foreground: цвет текста (проверка CSS переменных)
    -->
    <div class="flex h-screen w-full items-center justify-center bg-background text-foreground">
        <div class="text-center space-y-4">
            <h1 class="text-2xl font-bold">Zefirello MVP</h1>
            <p class="text-muted-foreground">System check...</p>

            <Button>
                Button works!
            </Button>
        </div>
    </div>
</template>
