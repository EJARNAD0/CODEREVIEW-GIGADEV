<?php
$donation_obj = new Donations();
$donation_id = isset($_GET['id']) ? $_GET['id'] : '';
$donation = $donation_obj->get_donation_details($donation_id);

if (!$donation) {
    echo "Donation not found.";
    exit;
}
?>

<h3>Donation Management</h3>
<div class="btn-box">
    <a class="btn-jsactive" onclick="document.getElementById('id01').style.display='block'">
        <i class="fas fa-user-edit"></i> Confirm Donation Receipt
    </a> 
</div>
<br/>
<div id="donation">
    <form method="POST" action="processes/process.donations.php?action=update">
        <div class="donation-block-half">
            <label for="donor_name">Donor Name</label>
            <input type="text" id="donor_name" class="input" name="donor_name" 
                   value="<?php echo htmlspecialchars($donation['user_firstname'] . ' ' . $donation['user_lastname']); ?>" 
                   disabled placeholder="Donor name..">

            <label for="amount">Amount</label>
            <input type="text" id="amount" class="input" name="amount" 
                   value="<?php echo htmlspecialchars($donation['amount']); ?>" 
                   disabled placeholder="Amount..">

            <label for="purpose">Purpose</label>
            <textarea id="purpose" class="input" name="purpose" 
                      placeholder="Purpose.." rows="4" disabled><?php echo htmlspecialchars($donation['purpose']); ?></textarea>
        </div>
    </form>
</div>

<!-- Confirm Donation Receipt Modal -->
<div id="id01" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Donation Receipt</h2>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to confirm that the donation has been received?</p>
            <form method="POST" action="processes/process.donations.php?action=update">
                <input type="hidden" name="donation_id" value="<?php echo $donation['donation_id']; ?>" />
                <label for="modal_status">Select New Status</label>
                <select id="modal_status" name="donation_status" required>
                    <option value="pending" <?php if ($donation['status'] == "Pending") { echo "selected"; } ?>>Pending</option>
                    <option value="received" <?php if ($donation['status'] == "Received") { echo "selected"; } ?>>Received</option>
                </select>
                <div class="modal-footer">
                    <button type="submit" class="confirmbtn">Confirm</button>
                    <button type="button" class="cancelbtn" onclick="document.getElementById('id01').style.display='none'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Donation Details Modal -->
<div id="id02" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update Donation Details</h2>
        </div>
        <div class="modal-body">
            <form method="POST" id="donationForm" action="processes/process.donations.php?action=updateDetails">
                <input type="hidden" name="donation_id" value="<?php echo $donation['donation_id']; ?>" />
                <label for="new_details">New Donation Purpose</label>
                <textarea id="new_details" class="input" name="new_details" placeholder="New purpose..."><?php echo htmlspecialchars($donation['purpose']); ?></textarea>
            </form>
            <div class="modal-footer">
                <button class="submitbtn" onclick="document.getElementById('donationForm').submit()">Confirm</button>
                <button type="button" class="cancelbtn" onclick="document.getElementById('id02').style.display='none'">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
  // Close modal if user clicks outside of it
  window.onclick = function(event) {
    if (event.target == document.getElementById('id01')) {
      document.getElementById('id01').style.display = "none";
    } else if (event.target == document.getElementById('id02')) {
      document.getElementById('id02').style.display = "none";
    }
  }
</script>
