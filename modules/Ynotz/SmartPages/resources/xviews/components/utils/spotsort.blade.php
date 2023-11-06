@props(['name', 'val' => 'none', 'exclusive' => 'true'])
<button type="button" x-data="{
        spotsort: '',
        options: ['none', 'asc', 'desc'],
        exclusive: {{$exclusive}},
        processClick() {
            for(let i = 0; i < this.options.length; i++) {
                if(this.options[i] == this.spotsort) {
                    this.spotsort = (i+1) < this.options.length ? this.options[i+1] : this.options[0];
                    break;
                }
            }
            $dispatch('spotsort', {exclusive: this.exclusive, data: { {{$name}}: this.spotsort}});
        },
        reset(sorts) {
            console.log('clearsort');
            console.log(sorts);
            console.log('$name: '+'{{$name}}');

            console.log(this.spotsort);
            console.log(Object.keys(sorts));
            if(!Object.keys(sorts).includes('{{$name}}')){
                console.log('not include');
                this.spotsort = 'none';
            }
            console.log(this.spotsort);
        }
    }"
    x-init="
    spotsort = '{{$val ?? 'none'}}';
    $watch('spotsort', (val) => {
        console.log('watching spotsort: '+ spotsort);
    })
        {{-- console.log('spot sort init');
        if(!options.includes(spotsort)) {
            spotsort = options['0'];
        }
        $dispatch('setsort', {exclusive: exclusive, data: { {{$name}}: spotsort}}); --}}
    "
    @click.prevent.stop="processClick"
    @clearsorts.window="reset($event.detail.sorts);"
    :class="spotsort=='none' || 'text-accent'"
    >
    <span x-show="spotsort==='none'"><x-easyadmin::display.icon icon="icons.up-down"/></span>
    <span x-show="spotsort==='asc'"><x-easyadmin::display.icon icon="icons.sort-up"/></span>
    <span x-show="spotsort==='desc'"><x-easyadmin::display.icon icon="icons.sort-down"/></span>
</button>
