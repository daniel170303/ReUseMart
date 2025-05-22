@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Pembeli</h2>
    <ul>
        @foreach ($pembelis as $pembeli)
            <li>
                <a href="#" class="pembeli-link" data-id="{{ $pembeli->id }}">{{ $pembeli->nama }}</a>
            </li>
        @endforeach
    </ul>
</div>

<!-- Modal -->
<div class="modal fade" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profilModalLabel">Profil Pembeli</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img id="fotoProfil" src="" alt="Foto Profil" class="img-thumbnail mb-3" width="150">
        <h5 id="namaPembeli"></h5>
        <p>Poin: <span id="poinPembeli"></span></p>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.pembeli-link');
    
    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;

            fetch(`/profil-pembeli/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('fotoProfil').src = `/storage/${data.foto}`;
                    document.getElementById('namaPembeli').textContent = data.nama;
                    document.getElementById('poinPembeli').textContent = data.poin;
                    const modal = new bootstrap.Modal(document.getElementById('profilModal'));
                    modal.show();
                });
        });
    });
});
</script>
@endsection