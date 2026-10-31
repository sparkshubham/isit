
   <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title message" id="">Delete?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <form class="delete-form" role="form"  onsubmit="deleteModal(1,1)">              
                    <div class="modal-body">           
                            <div class="row">
                                <div class="col-6"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button></div>
                                <div class="col-6"><button onclick="deleteModal(1,1)" type="button" id="savebtn" class="btn btn-primary">Yes</button></div>
                            </div>
                    </div>
            </div>
        </div>
    </div>
