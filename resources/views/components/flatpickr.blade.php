<input type="text" id="flatRemind" placeholder="Pick a date & time"
    class="placeholder:text-black border-2 placeholder:text-center w-full border-transparent hover:border-blue-800  ">

@once
    <script>
        let flatRemind = document.querySelector('#flatRemind');
        flatpickr(flatRemind, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
    </script>
@endonce
