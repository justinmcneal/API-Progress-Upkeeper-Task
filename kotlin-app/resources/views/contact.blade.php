<form action="{{ route('contact.send') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" id="username">
    </div>
  <div class="mb-3">
    <label class="form-label">Email address</label>
    <input type="email" class="form-control" id="email">
  </div>
  <div class="mb-3">
    <label  class="form-label">Feedback</label>
    <textarea name="textarea-name" id="textarea-id" rows="5" cols="30">
        This is the default text in the text area.
    </textarea>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>