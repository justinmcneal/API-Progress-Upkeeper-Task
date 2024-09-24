<form action="{{ route('registration.post') }}" method="POST"> 
    @csrf
    <div class="form-group">
        <h5>Username:</h5>
        <input type="text" name="username" required>
        <h5>Email:</h5>
        <input type="email" name="email" required>
        <h5>Password:</h5>
        <input type="password" name="password" required>
        <h5>Confirm Password:</h5>
        <input type="password" name="password_confirmation" required>
    </div>
    <button type="submit">Submit</button>
</form>
