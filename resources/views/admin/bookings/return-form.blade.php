<div class="modal fade" id="returnModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel{{ $booking->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel{{ $booking->id }}">Return Equipment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.bookings.return', $booking) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="equipment_condition">Equipment Condition</label>
                        <select name="equipment_condition" id="equipment_condition" class="form-control" required>
                            <option value="">Select Condition</option>
                            <option value="good">Good - Ready for next booking</option>
                            <option value="damaged">Damaged - Needs repair</option>
                            <option value="needs_maintenance">Needs Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="return_notes">Return Notes</label>
                        <textarea name="return_notes" id="return_notes" rows="3" class="form-control" placeholder="Enter any notes about the equipment's condition or the return"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Return</button>
                </div>
            </form>
        </div>
    </div>
</div> 