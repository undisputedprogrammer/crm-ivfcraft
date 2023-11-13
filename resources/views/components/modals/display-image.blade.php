<div x-data="{
    image: null
}"
@displayimage.window="
image = $event.detail.src;
showImage = true;
"
x-show="showImage" x-cloak x-transition class=" absolute w-full h-screen z-50 bg-neutral bg-opacity-70">

    <div class="md:w-[40%] h-fit rounded-lg bg-base-100 mx-auto mt-14 bg-opacity-100 flex flex-col p-4 space-y-1">
        <img :src="image" alt="WhatsApp Image" class=" self-center">

        <div class=" flex justify-start space-x-1 self-start">
            <a :href="image" download="" class=" btn btn-success btn-xs">Download</a>
            <button @click.prevent.stop="showImage = false" class=" btn btn-error btn-xs">Cancel</button>
        </div>
    </div>

</div>
