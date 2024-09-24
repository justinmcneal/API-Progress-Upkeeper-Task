<form action="{{ route('login.post') }}" method="POST">
    @csrf
  <div class="mb-3">
    <label class="form-label">Email address</label>
    <input type="email" class="form-control" id="email">
    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
  </div>
  <div class="mb-3">
    <label  class="form-label">Password</label>
    <input type="password" class="form-control" id="password">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>