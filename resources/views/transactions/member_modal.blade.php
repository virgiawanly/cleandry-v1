<div class="modal fade" role="dialog" id="modalMember">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center justify-content-between mt-1 mb-3">
                    <a href="/members" target="_blank" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i>
                        <span>Register member</span>
                    </a>
                    <button class="btn btn-link" onclick="syncMembers()">
                        <i class="fas fa-sync mr-1"></i>
                        <span>Sinkron Data</span>
                    </button>
                </div>
                <table id="membersTable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Nomor Telepon</th>
                            <th>Alamat</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data goes here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
