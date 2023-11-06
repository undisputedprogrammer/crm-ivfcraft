<template x-if=" qnas != null && qnas.length != 0">
<div class=" flex flex-col">

    {{-- <p class=" text-base font-medium text-secondary">QNA</p> --}}

    <ul class=" text-sm  font-normal">
        <template x-for="(a,i) in Object.keys(qnas)">

            <li>
                <p class="text-warning font-medium opacity-70">
                    <span x-text="i+1+'. '"></span>
                    <span class="" x-text="(a[0].toUpperCase() +
                    a.slice(1)).replace(/_/g, ' ')+'?'"></span>
                </p>
                <p class="px-3" x-text="qnas[a]"></p>
            </li>

        </template>
    </ul>

</div>
</template>
