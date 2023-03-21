@extends('layouts.front_signature_form')

@section('content')

<form id="signedForm" method="POST" action="{{ route('setSignature') }}">
@csrf
<center>
    <canvas id="canvas" width="700" height="350" style="border:solid black 1px;background: #fff;margin: 10px;">
        Your browser does not support the element canvas.
    </canvas>
</center> 
<input type="hidden" id="signed" name="signed">
<input type="hidden" name="type" value="{{ $type }}">
<input type="hidden" name="id" value="{{ $id }}">
<input type="hidden" name="create_new" value="{{ $create_new }}">
<input type="hidden" name="cancel" value="{{ $cancel }}">
<input type="hidden" name="quantity_sender" value="1">
<input type="hidden" name="quantity_recipient" value="1">

<script type="text/javascript">
    const type = "{{ $type }}"
    if (type) {
        const textHtml = `<input type="hidden" name="{{ $type }}" value="{{ $id }}">`;            
        document.forms["signedForm"].insertAdjacentHTML('beforeend', textHtml);
    }    
</script>

@if (isset($_GET['worksheet_id']))
<script type="text/javascript">
    localStorage.setItem('worksheet_id', "{{$_GET['worksheet_id']}}")
</script>
@elseif (isset($_GET['eng_worksheet_id']))
<script type="text/javascript">
    localStorage.setItem('eng_worksheet_id', "{{$_GET['eng_worksheet_id']}}")
</script>
@elseif (isset($_GET['draft_id']))
<script type="text/javascript">
    localStorage.setItem('draft_id', "{{$_GET['draft_id']}}")
</script>
@elseif (isset($_GET['eng_draft_id']))
<script type="text/javascript">
    localStorage.setItem('eng_draft_id', "{{$_GET['eng_draft_id']}}")
</script>
@endif 

@if (isset($_GET['form_screen']))
<script type="text/javascript">
    localStorage.setItem('form_screen', "{{$_GET['form_screen']}}")
</script>
@endif 

@if (isset($_GET['document_id']))
<script type="text/javascript">
    localStorage.setItem('document_id', "{{$_GET['document_id']}}")
</script>
@endif 

<input type="hidden" id="document_id" name="document_id" value="">
<input type="hidden" id="form_screen" name="form_screen" value="">
<input type="hidden" id="session_token" name="session_token" value="">

@if (isset($_GET['user_name']))
<input type="hidden" name="user_name" value="{{$_GET['user_name']}}">
@else
<input type="hidden" name="user_name" value="">
@endif

</form>

<div class="container">
    <center>
        <button class="btn btn-primary" id="clearSign" style="margin: 20px;">Clear / Очистить</button>
        <button class="btn btn-success" id="saveSigned" style="margin: 20px;">Save / Сохранить</button>
    </center>   
</div>

@if (isset($_GET['session_token']))
<script type="text/javascript">
    localStorage.setItem('session_token', "{{$_GET['session_token']}}")
    $.ajax({
        type:'POST',
        url:"{{ url('/api/check-temp-table') }}",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {"session_token":"{{$_GET['session_token']}}"},
        success:function(data){
            console.log(data)
            if (data === 'true') {
                document.getElementById("saveSigned").disabled = false
                console.log("{{$_GET['session_token']}}") 
            }  
            else document.getElementById("saveSigned").disabled = true             
        },
        error: function (msg){
            alert('Error')
        }
    }); 
    
</script>
@elseif ($cancel)
<script type="text/javascript">
    document.getElementById("saveSigned").disabled = false 
</script>
@else
<script type="text/javascript">
    document.getElementById("saveSigned").disabled = true 
</script>
@endif

    <!-- <br>
    Log: <pre id="log" style="border: 1px solid #ccc;"></pre> -->
    
    <script>

        if (localStorage.getItem('worksheet_id')) {
            const textHtml = `<input type="hidden" name="worksheet_id" id="worksheet_id" value="`+localStorage.getItem('worksheet_id')+`">`;            
            document.forms["signedForm"].insertAdjacentHTML('beforeend', textHtml);
        }
        else if (localStorage.getItem('eng_worksheet_id')) {
            const textHtml = `<input type="hidden" name="eng_worksheet_id" id="eng_worksheet_id" value="`+localStorage.getItem('eng_worksheet_id')+`">`;            
            document.forms["signedForm"].insertAdjacentHTML('beforeend', textHtml);
        }
        else if (localStorage.getItem('draft_id')) {
            const textHtml = `<input type="hidden" name="draft_id" id="draft_id" value="`+localStorage.getItem('draft_id')+`">`;            
            document.forms["signedForm"].insertAdjacentHTML('beforeend', textHtml);
        }
        else if (localStorage.getItem('eng_draft_id')) {
            const textHtml = `<input type="hidden" name="eng_draft_id" id="eng_draft_id" value="`+localStorage.getItem('eng_draft_id')+`">`;            
            document.forms["signedForm"].insertAdjacentHTML('beforeend', textHtml);
        }

        if (localStorage.getItem('form_screen')) {            
            document.getElementById("form_screen").value = localStorage.getItem('form_screen');
        }

        if (localStorage.getItem('document_id')) {            
            document.getElementById("document_id").value = localStorage.getItem('document_id');
        }

        if (localStorage.getItem('session_token')) {            
            document.getElementById("session_token").value = localStorage.getItem('session_token');
        }
        
        var phoneExist = null;        
        
        function startup() {           
            var windowWidth = window.screen.width;

            if (windowWidth > 767) {
                var canvas = document.getElementById("canvas"), 
                context = canvas.getContext("2d"),
                w = canvas.width,
                h = canvas.height;

                var mouse = {x:0, y:0};
                var draw = false;

                canvas.addEventListener("mousedown", function(e){
                    mouse.x = e.pageX - this.offsetLeft;
                    mouse.y = e.pageY - this.offsetTop;
                    draw = true;
                    context.beginPath();
                    context.moveTo(mouse.x, mouse.y);
                });
                
                canvas.addEventListener("mousemove", function(e){
                    if(draw==true){
                        mouse.x = e.pageX - this.offsetLeft;
                        mouse.y = e.pageY - this.offsetTop;
                        context.lineTo(mouse.x, mouse.y);
                        context.stroke();
                    }
                });
                
                canvas.addEventListener("mouseup", function(e){
                    mouse.x = e.pageX - this.offsetLeft;
                    mouse.y = e.pageY - this.offsetTop;
                    context.lineTo(mouse.x, mouse.y);
                    context.stroke();
                    context.closePath();
                    draw = false;
                });
            }
            else{
                var el = document.getElementById("canvas");
                el.width = windowWidth - 20;
                el.height = (el.width) / 2;
                el.addEventListener("touchstart", handleStart, false);
                el.addEventListener("touchend", handleEnd, false);
                el.addEventListener("touchcancel", handleCancel, false);
                el.addEventListener("touchmove", handleMove, false);
            }


            var saveSigned = document.getElementById("saveSigned");
            saveSigned.addEventListener("click", function(e){
                saveSigned.disabled = true;
                let canvas = document.getElementById("canvas");
                document.getElementById('signed').value = canvas.toDataURL('image/png');
                localStorage.clear();
                document.forms["signedForm"].submit();                
            });


            var clearSign = document.getElementById("clearSign");
            clearSign.addEventListener("click", function(e){
                let canvas = document.getElementById("canvas");
                context = canvas.getContext("2d");      
                context.clearRect(0, 0, canvas.width, canvas.height);        
            });
            
        }       

        document.addEventListener("DOMContentLoaded", startup);
        var ongoingTouches = [];

        
        function handleStart(evt) {
            evt.preventDefault();
            //console.log("touchstart.");
            var el = document.getElementById("canvas");
            var ctx = el.getContext("2d");
            var touches = evt.changedTouches;

            for (var i = 0; i < touches.length; i++) {
                //console.log("touchstart:" + i + "...");
                ongoingTouches.push(copyTouch(touches[i]));
                var color = colorForTouch(touches[i]);
                ctx.beginPath();
                //ctx.arc(touches[i].pageX, touches[i].pageY, 4, 0, 2 * Math.PI, false);  // a circle at the start
                ctx.fillStyle = color;
                ctx.fill();
                //console.log("touchstart:" + i + ".");
            }
        }

        
        function handleMove(evt) {
            evt.preventDefault();
            var el = document.getElementById("canvas");
            var ctx = el.getContext("2d");
            var touches = evt.changedTouches;

            for (var i = 0; i < touches.length; i++) {
                var color = colorForTouch(touches[i]);
                var idx = ongoingTouchIndexById(touches[i].identifier);

                if (idx >= 0) {
                    //console.log("continuing touch "+idx);
                    ctx.beginPath();
                    //console.log("ctx.moveTo(" + ongoingTouches[idx].pageX + ", " + ongoingTouches[idx].pageY + ");");
                    ctx.moveTo(ongoingTouches[idx].pageX, ongoingTouches[idx].pageY);
                    //console.log("ctx.lineTo(" + touches[i].pageX + ", " + touches[i].pageY + ");");
                    ctx.lineTo(touches[i].pageX, touches[i].pageY);
                    ctx.lineWidth = 4;
                    ctx.strokeStyle = color;
                    ctx.stroke();

                    ongoingTouches.splice(idx, 1, copyTouch(touches[i]));  // swap in the new touch record
                    //console.log(".");
                } else {
                    //console.log("can't figure out which touch to continue");
                }
            }
        }

        
        function handleEnd(evt) {
            evt.preventDefault();
            log("touchend");
            var el = document.getElementById("canvas");
            var ctx = el.getContext("2d");
            var touches = evt.changedTouches;

            for (var i = 0; i < touches.length; i++) {
                var color = colorForTouch(touches[i]);
                var idx = ongoingTouchIndexById(touches[i].identifier);

                if (idx >= 0) {
                    ctx.lineWidth = 4;
                    ctx.fillStyle = color;
                    ctx.beginPath();
                    ctx.moveTo(ongoingTouches[idx].pageX, ongoingTouches[idx].pageY);
                    ctx.lineTo(touches[i].pageX, touches[i].pageY);
                    //ctx.fillRect(touches[i].pageX - 4, touches[i].pageY - 4, 8, 8);  // and a square at the end
                    ongoingTouches.splice(idx, 1);  // remove it; we're done
                } else {
                  //console.log("can't figure out which touch to end");
                }
            }
        }


        function handleCancel(evt) {
            evt.preventDefault();
            //console.log("touchcancel.");
            var touches = evt.changedTouches;

            for (var i = 0; i < touches.length; i++) {
                var idx = ongoingTouchIndexById(touches[i].identifier);
                ongoingTouches.splice(idx, 1);  // remove it; we're done
            }
        }


        function colorForTouch(touch) {
            var r = touch.identifier % 16;
            var g = Math.floor(touch.identifier / 3) % 16;
            var b = Math.floor(touch.identifier / 7) % 16;
            r = r.toString(16); // make it a hex digit
            g = g.toString(16); // make it a hex digit
            b = b.toString(16); // make it a hex digit
            var color = "#" + r + g + b;
            //console.log("color for touch with identifier " + touch.identifier + " = " + color);
            return color;
        }


        function copyTouch({ identifier, pageX, pageY }) {
            return { identifier, pageX, pageY };
        }


        function ongoingTouchIndexById(idToFind) {
            for (var i = 0; i < ongoingTouches.length; i++) {
                var id = ongoingTouches[i].identifier;

                if (id == idToFind) {
                    return i;
                }
            }
            return -1;    // not found
        }


        function log(msg) {
            /*var p = document.getElementById('log');
            p.innerHTML = msg + "\n" + p.innerHTML;*/
        }

    </script>

@endsection
