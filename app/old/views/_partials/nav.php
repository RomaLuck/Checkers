<ul class="nav justify-content-end shadow">
    <li>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-outline-success m-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Create game
        </button>
    </li>
    <li class="nav-item">
        <a href="/" class="btn btn-outline-primary m-2">Game list</a>
    </li>
    <li class="nav-item">
        <a href="/logout" class="btn btn-outline-danger m-2">Exit</a>
    </li>
</ul>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-2">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Choose team</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/create">
                <div class="mt-2 d-flex">
                    <button type="button" class="btn btn-light shadow" id="whiteButton">
                        <input class="form-check-input" type="radio" name="player" value="white" id="whiteRadio"
                               hidden="hidden">
                        <img class="w-25" src="pictures/white.png" alt="white">
                        <label class="form-check-label" for="whiteRadio">

                        </label>
                    </button>
                    <button type="button" class="btn btn-light shadow" id="blackButton">
                        <input class="form-check-input" type="radio" name="player" value="black" id="blackRadio"
                               hidden="hidden">
                        <img class="w-25" src="pictures/black.png" alt="black">
                        <label class="form-check-label" for="blackRadio">

                        </label>
                    </button>
                    <p class="text-danger small text-center mt-3" id="alerts"></p>
                </div>
                <div class="modal-footer row d-flex justify-content-center">
                    <button type="submit" class="btn btn-danger" id="save">Create new game</button>
                </div>
            </form>
        </div>
    </div>
</div>

