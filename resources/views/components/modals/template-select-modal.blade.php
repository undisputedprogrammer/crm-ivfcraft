@props(['templates'])
<div x-show="showTemplateModal" x-cloak class="fixed top-0 left-0 w-full h-full bg-black opacity-50 z-40"></div>

    <!-- Modal container -->
    <div x-cloak x-show="showTemplateModal" x-transition class="fixed top-0 left-0 w-full h-full flex items-start mt-8 justify-center z-50">
        <div class="bg-base-100 rounded-lg p-8 shadow-lg w-1/3">
            <!-- Close button -->
            <button class="absolute top-2 right-2 text-gray-600 hover:text-gray-800" id="closeModal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Form inside the modal -->
            <form @submit.prevent.stop="sendBulkMessage(ajaxLoading)" action="" method="">
                <div class="mb-4">
                    <label for="selectInput" class="block text-primary text-base font-semibold mb-2">Select a template:</label>
                    @if ($templates != null && count($templates)>0)
                        <select id="selectTemplate" name="template" required class="w-full select bg-base-200 text-base-content">
                            @foreach ($templates as $template)
                                <option value="{{$template->id}}">{{$template->template}}</option>
                            @endforeach
                        </select>
                        @else
                        <p class=" font-medium text-base text-center text-base-content">No templates available for now!</p>
                    @endif

                </div>
                <div class="flex space-x-2">
                    <button type="submit" @click="toggleTemplateModal()" class=" btn btn-success btn-sm">Submit</button>
                    <button @click.prevent.stop="toggleTemplateModal()" class=" btn btn-error btn-sm">Close</button>
                </div>
            </form>
        </div>
    </div>
