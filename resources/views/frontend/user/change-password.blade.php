
<!-- Change Password Modal -->
<div class="modal fade" id="ChangePassword" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ChangePasswordlLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="ChangePasswordlLabel">Change Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="create-post-form">
               <form method="POST"  action="{{ route('frontend-changePassword') }}" id="change-password-form" autocomplete="false">
                        @csrf
                    <div class="form-input">
                        <input name="old_password" type="password" value="" class="form-control" id="OldPassword" placeholder="Current Password" />
                        <span class="eyeicon">
                            <i toggle="#OldPassword" class="uil uil-eye" id="show_eye"></i>
                        </span>
                    </div>
                    <div class="form-input">
                        <input name="new_password" type="password" value="" class="form-control" id="new_password" placeholder="New Password" />
                        <span class="eyeicon">
                            <i toggle="#new_password" class="uil uil-eye" id="show_eye"></i>
                        </span>
                    </div>
                    <div class="form-input">
                        <input name="confirm_password" type="password" value="" class="form-control" id="confirm_password" placeholder="Confirm New Password" />
                        <span class="eyeicon">
                            <i toggle="#confirm_password" class="uil uil-eye" id="show_eye"></i>
                        </span>
                    </div>
                    <div class="row mt-4 pt-2">
                        <div class="col-6"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
                        <div class="col-6"><button type="submit" class="btn btn-primary">Update</button></div>
                    </div>
                </form>
            </div>  
        </div>
    </div>
    </div>
</div>
