<form action="" class=" flex space-x-1 items-center bg-neutral p-1 rounded-lg" id="filter-by-creation-date-form"
@submit.prevent.stop="filterByCreationDate($el);">
    <input type="date" :value="creation_date != null ? creation_date : null" name="creation_date" class=" input input-sm text-base-content font-medium">
    <button type="submit" class=" btn btn-sm btn-primary">Filter</button>
</form>
