document.addEventListener('DOMContentLoaded', function () {

    // dropdown nav
    document.querySelectorAll('.nav-dropdown').forEach(function (dropdown) {
        const link = dropdown.querySelector('.nav-link');

        if (link) {
            link.addEventListener('click', function (e) {
                if (link.getAttribute('href') === '#') e.preventDefault();

                const jeOtvoren = dropdown.classList.contains('otvoren');
                document.querySelectorAll('.nav-dropdown.otvoren').forEach(function (d) {
                    d.classList.remove('otvoren');
                });
                if (!jeOtvoren) dropdown.classList.add('otvoren');
            });
        }
    });

    // zatvori dropdown klikom izvan navigacije
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-dropdown')) {
            document.querySelectorAll('.nav-dropdown.otvoren').forEach(function (d) {
                d.classList.remove('otvoren');
            });
        }
    });


    // mob nav
    const hamburger = document.getElementById('hamburger');
    const navLista  = document.getElementById('navLista');

    if (hamburger && navLista) {
        hamburger.addEventListener('click', function () {
            hamburger.classList.toggle('aktivan');
            navLista.classList.toggle('otvoreno');
        });

        document.addEventListener('click', function (e) {
            if (!hamburger.contains(e.target) && !navLista.contains(e.target)) {
                hamburger.classList.remove('aktivan');
                navLista.classList.remove('otvoreno');
            }
        });
    }


    // scroll navigacije
    const nav = document.getElementById('navigacija');
    if (nav) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                nav.style.boxShadow = '0 4px 24px rgba(0,0,0,0.1)';
                nav.style.borderBottomColor = 'transparent';
            } else {
                nav.style.boxShadow = 'none';
                nav.style.borderBottomColor = 'rgba(0,0,0,0.06)';
            }
        });
    }


    // galerija
    document.querySelectorAll('.carousel-omotac').forEach(function (omotac) {
        const traka   = omotac.querySelector('.carousel-traka');
        const lijevi  = omotac.querySelector('.carousel-gumb.lijevi');
        const desni   = omotac.querySelector('.carousel-gumb.desni');

        if (!traka) return;

        let pozicija = 0; 

        function dobaviBrVidljivih() {
            if (window.innerWidth <= 768) return 1;
            if (window.innerWidth <= 992) return 2;
            return 3;
        }

        function ukupnoStavki() {
            return traka.querySelectorAll('.carousel-item-custom').length;
        }

        function pomakniCarousel() {
            const vidljivo   = dobaviBrVidljivih();
            const sirStavke  = traka.querySelector('.carousel-item-custom');
            if (!sirStavke) return;
            const pomak = (sirStavke.offsetWidth + 20) * pozicija;
            traka.style.transform = `translateX(-${pomak}px)`;
        }

        if (desni) {
            desni.addEventListener('click', function () {
                const vidljivo = dobaviBrVidljivih();
                const max = Math.max(0, ukupnoStavki() - vidljivo);
                if (pozicija < max) { pozicija++; pomakniCarousel(); }
            });
        }
        if (lijevi) {
            lijevi.addEventListener('click', function () {
                if (pozicija > 0) { pozicija--; pomakniCarousel(); }
            });
        }

        window.addEventListener('resize', function () { pozicija = 0; pomakniCarousel(); });
    });


    // ajax dodavanje/uklanjanje favorita
    document.querySelectorAll('.btn-favorit').forEach(function (gumb) {
        gumb.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const djeloId = gumb.dataset.djeloId;
            const izvor   = gumb.dataset.izvor;

            fetch('/monetica/ajax/favorit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'djelo_id=' + encodeURIComponent(djeloId) +
                      '&izvor='   + encodeURIComponent(izvor)
            })
            .then(function (odgovor) { return odgovor.json(); })
            .then(function (podaci) {
                if (podaci.status === 'dodan') {
                    gumb.classList.add('aktivan');
                    gumb.innerHTML = '<i class="fa fa-heart"></i>';
                    gumb.title = 'Ukloni iz favorita';
                } else if (podaci.status === 'uklonjen') {
                    gumb.classList.remove('aktivan');
                    gumb.innerHTML = '<i class="fa fa-heart"></i>';
                    gumb.title = 'Dodaj u favorite';
                } else if (podaci.status === 'greska_prijava') {
                    window.location.href = '/monetica/prijava.php?poruka=morate_se_prijaviti';
                }
            })
            .catch(function () {
                window.location.href = '/monetica/prijava.php';
            });
        });
    });


    // live pretraga galerije
    const pretragaUnos = document.getElementById('pretragaUnos');
    if (pretragaUnos) {
        pretragaUnos.addEventListener('input', function () {
            const upit = this.value.toLowerCase();
            document.querySelectorAll('.galerija-kartica').forEach(function (kartica) {
                const naslov  = (kartica.dataset.naslov  || '').toLowerCase();
                const autor   = (kartica.dataset.autor   || '').toLowerCase();
                const kateg   = (kartica.dataset.kategorija || '').toLowerCase();
                const vidljiv = naslov.includes(upit) || autor.includes(upit) || kateg.includes(upit);
                kartica.style.display = vidljiv ? '' : 'none';
            });
            const prikaz = document.getElementById('bezRezultata');
            if (prikaz) {
                const imaPrikazanih = [...document.querySelectorAll('.galerija-kartica')]
                    .some(k => k.style.display !== 'none');
                prikaz.style.display = imaPrikazanih ? 'none' : 'block';
            }
        });
    }


    //filter po kategoriji
    document.querySelectorAll('.filter-gumb').forEach(function (gumb) {
        gumb.addEventListener('click', function () {
            document.querySelectorAll('.filter-gumb').forEach(g => g.classList.remove('aktivan'));
            this.classList.add('aktivan');

            const odabraKateg = this.dataset.kategorija;

            document.querySelectorAll('.galerija-kartica').forEach(function (kartica) {
                if (!odabraKateg || odabraKateg === 'sve') {
                    kartica.style.display = '';
                } else {
                    const kateg = (kartica.dataset.kategorija || '').toLowerCase();
                    kartica.style.display = kateg === odabraKateg.toLowerCase() ? '' : 'none';
                }
            });
        });
    });


    // potvrda brisanja (admin) 
    document.querySelectorAll('.btn-brisi').forEach(function (gumb) {
        gumb.addEventListener('click', function (e) {
            const naziv = gumb.dataset.naziv || 'ovaj zapis';
            // traži potvrdu od korisnika prije brisanja
            if (!confirm('Jeste li sigurni da želite obrisati "' + naziv + '"?\nOva radnja se ne može poništiti.')) {
                e.preventDefault();
            }
        });
    });


    // pregled slike prije uploada
    const uploadPolje = document.getElementById('slikaUpload');
    const pregledSlike = document.getElementById('pregledSlike');
    if (uploadPolje && pregledSlike) {
        uploadPolje.addEventListener('change', function () {
            const datoteka = this.files[0];
            if (datoteka) {
                // čitaj datoteku i prikaži preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    pregledSlike.src = e.target.result;
                    pregledSlike.style.display = 'block';
                };
                reader.readAsDataURL(datoteka);
            }
        });
    }


    // lightbox za lokalna djela 
    const lightbox       = document.getElementById('lightboxModal');
    const lightboxSlika  = document.getElementById('lightboxSlika');
    const lightboxZatvori = document.getElementById('lightboxZatvori');

    if (lightbox) {
        document.querySelectorAll('.lokalna-slika-klik').forEach(function (slika) {
            slika.addEventListener('click', function () {
                lightboxSlika.src = this.src;
                lightboxSlika.alt = this.alt;
                document.getElementById('lightboxNaslov').textContent    = this.dataset.naslov    || '';
                document.getElementById('lightboxAutor').textContent     = this.dataset.autor     || '';
                document.getElementById('lightboxGodina').textContent    = this.dataset.godina    || '';
                document.getElementById('lightboxTehnika').textContent   = this.dataset.tehnika   || '';
                document.getElementById('lightboxDimenzije').textContent = this.dataset.dimenzije || '';
                document.getElementById('lightboxOpis').textContent      = this.dataset.opis      || '';

                ['lightboxGodina','lightboxTehnika','lightboxDimenzije','lightboxOpis'].forEach(function (id) {
                    const el = document.getElementById(id);
                    el.style.display = el.textContent.trim() ? '' : 'none';
                });

                lightbox.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        lightboxZatvori.addEventListener('click', function () {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        });

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                lightbox.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && lightbox.style.display !== 'none') {
                lightbox.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }


    // automatsko sakrivanje poruka
    setTimeout(function () {
        document.querySelectorAll('.poruka-uspjeh:not(.ne-skrivaj), .poruka-info:not(.ne-skrivaj)').forEach(function (el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);


    // glatko scrollanje na sidra
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            const ciljId = this.getAttribute('href');
            const cilj = document.querySelector(ciljId);
            if (cilj) {
                e.preventDefault();
                cilj.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});
