@props(['leads'])
<div class=" w-[96%] lg:w-1/2">
    <div class="overflow-x-auto border border-primary rounded-xl">
        @if ($leads != null && count($leads)>0)

        <table
        x-data="{
            toggleAll(){
                let main = document.getElementById('main-checkbox');
                let checkboxes = document.getElementsByClassName('checkboxes');
                Array.from(checkboxes).forEach(function(checkbox){
                    if(main.checked){
                        if(!checkbox.checked){
                            checkbox.click();
                        }
                    }
                    else{
                        if(checkbox.checked){
                            checkbox.click();
                        }
                    }
                })
            }
        }"
        class="table ">
          <!-- head -->
          <thead>
            <tr class=" text-secondary ">
                <th>
                    <label>
                      <input @click="toggleAll()" id="main-checkbox" type="checkbox" class="checkbox checkbox-secondary " />
                    </label>
                </th>
              <th>Lead ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Agent</th>
            </tr>
          </thead>
          <tbody>

            @foreach ($leads as $lead)
                <tr id="r-{{$lead->id}}" class="text-base-content hover:bg-base-100 relative">
                    <th>
                        <label>
                          <input id="c-{{$lead->id}}" @change="
                          if($el.checked){
                            selected.push($el.id.substring(2))
                            console.log(selected)
                          }else{
                            selected.splice(selected.indexOf($el.id.substring(2)),1)
                            console.log(selected)
                          }
                          " type="checkbox" class="checkbox checkbox-secondary checkbox-sm checkboxes" />
                        </label>
                    </th>
                    <td>{{$lead->id}}</td>
                    <td>{{$lead->name}}</td>
                    <td>{{$lead->email}}</td>
                    <td>{{$lead->assigned->name}}</td>
                </tr>

            @endforeach
          </tbody>
        </table>

        @else
            <h1 class=" font-semibold text-lg text-neutral-content p-4">No leads to show</h1>
        @endif


      </div>
    <div class="mt-1.5">
        {{ $leads->links() }}
    </div>

</div>
