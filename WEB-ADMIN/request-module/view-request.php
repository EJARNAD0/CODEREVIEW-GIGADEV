<?php
$request = new Request();
$request_id = isset($_GET['id']) ? $_GET['id'] : '';
$req = $request->get_request_details($request_id);

if (!$req) {
    echo "Request not found.";
    exit;
}
?>
<h3>Request Management</h3>
<div class="btn-box">
    <a class="btn-jsactive" onclick="document.getElementById('id01').style.display='block'">
        <i class="fas fa-user-edit"></i> Update Request Status
    </a> 
</div>
<br/>
<div id="request">
    <form method="POST" action="processes/process.request.php?action=update">
        <div class="request-block-half">
            <label for="requester_name">Requester Name</label>
            <input type="text" id="requester_name" class="input" name="requester_name" 
                   value="<?php echo htmlspecialchars($req['user_firstname'] . ' ' . $req['user_lastname']); ?>" 
                   disabled placeholder="Requester name..">

            <label for="request_details">Request Details</label>
            <textarea id="request_details" class="input" name="request_details" 
                      placeholder="Details.." rows="4" disabled><?php echo htmlspecialchars($req['request_details']); ?></textarea>

            <label for="current_status">Current Status</label>
            <input type="text" id="current_status" class="input" name="current_status" 
                   value="<?php echo htmlspecialchars(ucfirst($req['request_status'])); ?>" 
                   disabled placeholder="Current status..">
        </div>
    </form>
</div>

<div id="id01" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Update Request Status</h2>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to update the request status?</p>
      <form method="POST" action="processes/process.request.php?action=status">
        <input type="hidden" name="requestid" value="<?php echo $req['request_id']; ?>" />
        <label for="modal_status">Select New Status</label>
        <select id="modal_status" name="request_status">
            <option value="pending" <?php if ($req['request_status'] == "Pending") { echo "selected"; } ?>>Pending</option>
            <option value="approved" <?php if ($req['request_status'] == "Approved") { echo "selected"; } ?>>Approved</option>
            <option value="rejected" <?php if ($req['request_status'] == "Rejected") { echo "selected"; } ?>>Rejected</option>
        </select>
        <div class="modal-footer">
            <button type="submit" class="confirmbtn">Confirm</button>
            <button type="button" class="cancelbtn" onclick="document.getElementById('id01').style.display='none'">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="id02" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Update Request Details</h2>
    </div>
    <div class="modal-body">
      <form method="POST" id="requestForm" action="processes/process.request.php?action=updateDetails">
        <input type="hidden" name="request_id" value="<?php echo $req['request_id']; ?>" />
        <label for="new_details">New Request Details</label>
        <textarea id="new_details" class="input" name="new_details" placeholder="New details..."><?php echo htmlspecialchars($req['request_details']); ?></textarea>
      </form>
      <div class="modal-footer">
        <button class="submitbtn" onclick="document.getElementById('requestForm').submit()">Confirm</button>
        <button class="cancelbtn" onclick="document.getElementById('id02').style.display='none'">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
  window.onclick = function(event) {
    if (event.target == document.getElementById('id01')) {
      document.getElementById('id01').style.display = "none";
    } else if(event.target == document.getElementById('id02')){
      document.getElementById('id02').style.display = "none";
    }
  }
</script>
