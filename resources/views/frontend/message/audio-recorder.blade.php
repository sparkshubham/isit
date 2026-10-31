

 <!-- Create Chat Room Modal -->

 <div class="modal fade" id="audiorecorder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="CreateChatRoomlLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:588px!important">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CreateChatRoomlLabel">Audio Recorder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="create-post-form">
                        <div class="app">
                        <select name="" id="micSelect"></select>

                        <select id="visSelect">
                            <option value="frequencybars">Bar</option>
                            <option value="sinewave">Wave</option>
                            <option value="circle">Circle</option>
                        </select>

                        <a id="download" >Download</a>

                        <div class="audio-controls">
                            <button id="record">Record</button>
                            <button id="stop">Stop</button>
                            <audio id="audio" controls></audio>
                        </div>

                        <div id="msg"></div>
                        <canvas width="500" height="300"></canvas>
                        <div>

                  
                   
                </div>            
                <div class="row">
                    <div class="col-12"><button type="button" id="send-message-audio"  data-bs-dismiss="modal"class="btn btn-secondary">Send Audio</button></div>
                  
                </div>
            </div>
        </div>
    </div>
</div>

