@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Create New Job</h1>
        <div class="sub">Add a new customer transport request</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('jobs.index') }}" class="btn btn-outline-navy"><i class="bi bi-arrow-left me-1"></i> Back to Jobs</a>
      </div>
    </div>

    <div class="panel">
      <form action="{{ route('jobs.store') }}" method="POST">
        @csrf
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="job_number" class="form-label">Job Number</label>
            <input type="text" class="form-control" name="job_number" id="job_number" required>
            <small class="text-muted">Leave empty to auto-generate</small>
          </div>
          <div class="col-md-6 mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select class="form-select" name="customer_id" id="customer_id" required>
              <option value="">Select Customer</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="pickup_location" class="form-label">Pickup Location</label>
            <input type="text" class="form-control" name="pickup_location" id="pickup_location" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="delivery_location" class="form-label">Delivery Location</label>
            <input type="text" class="form-control" name="delivery_location" id="delivery_location" required>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="job_date" class="form-label">Job Date</label>
            <input type="date" class="form-control" name="job_date" id="job_date" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" name="status" id="status" required>
              <option value="pending">Pending</option>
              <option value="assigned">Assigned</option>
              <option value="in_transit">In Transit</option>
              <option value="delivered">Delivered</option>
              <option value="delayed">Delayed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="vehicle_id" class="form-label">Vehicle (Optional)</label>
            <select class="form-select" name="vehicle_id" id="vehicle_id">
              <option value="">Select Vehicle</option>
              @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}">{{ $vehicle->reg_no }} - {{ $vehicle->make_model }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label for="driver_id" class="form-label">Driver (Optional)</label>
            <select class="form-select" name="driver_id" id="driver_id">
              <option value="">Select Driver</option>
              @foreach($drivers as $driver)
                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="quoted_price" class="form-label">Quoted Price (PKR)</label>
            <input type="number" step="0.01" class="form-control" name="quoted_price" id="quoted_price" placeholder="0.00">
          </div>
          <div class="col-md-6 mb-3">
            <label for="bags" class="form-label">Bags</label>
            <input type="number" class="form-control" name="bags" id="bags" placeholder="0">
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="rent" class="form-label">Rent (PKR)</label>
            <input type="number" step="0.01" class="form-control" name="rent" id="rent" placeholder="0.00">
          </div>
          <div class="col-md-4 mb-3">
            <label for="advance" class="form-label">Advance (PKR)</label>
            <input type="number" step="0.01" class="form-control" name="advance" id="advance" placeholder="0.00">
          </div>
          <div class="col-md-4 mb-3">
            <label for="advance_date" class="form-label">Advance Date</label>
            <input type="date" class="form-control" name="advance_date" id="advance_date">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="dues" class="form-label">Dues (PKR)</label>
            <input type="number" step="0.01" class="form-control" name="dues" id="dues" placeholder="0.00">
          </div>
        </div>

        <div class="mb-3">
          <label for="notes" class="form-label">Notes</label>
          <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Create Job</button>
          <a href="{{ route('jobs.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>

    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
@endsection