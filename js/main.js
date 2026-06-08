document.addEventListener('DOMContentLoaded', function () {

    //dropdown 
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
  
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-dropdown')) {
            document.querySelectorAll('.nav-dropdown.otvoren').forEach(function (d) {
                d.classList.remove('otvoren');
            });
        }
    });


    // hamburger
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


    //navigacija pri scrollu
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


    // galerije
    document.querySelectorAll('.carousel-omotac').forEach(function (omotac) {
        var traka  = omotac.querySelector('.carousel-traka');
        var lijevi = omotac.querySelector('.carousel-gumb.lijevi');
        var desni  = omotac.querySelector('.carousel-gumb.desni');

        if (!traka || traka.querySelectorAll('.carousel-item-custom').length < 2) return;

        var radi = false; 

        function slot() {
            var s = traka.querySelectorAll('.carousel-item-custom');
            return s[1].offsetLeft - s[0].offsetLeft;
        }

        function postaviTrenutak(px) {
            traka.style.transition = 'none';
            traka.style.transform  = 'translateX(' + px + 'px)';
            void traka.offsetHeight; // reflow — prisili browser da primijeni prije vracanja tranzicije
            traka.style.transition  = '';
        }

        if (desni) {
            desni.addEventListener('click', function () {
                if (radi) return;
                radi = true;
                traka.style.transform = 'translateX(' + (-slot()) + 'px)';
                traka.addEventListener('transitionend', function handler(e) {
                    if (e.propertyName !== 'transform') return;
                    traka.removeEventListener('transitionend', handler);
                    traka.appendChild(traka.querySelector('.carousel-item-custom'));
                    postaviTrenutak(0);
                    radi = false;
                });
            });
        }

        if (lijevi) {
            lijevi.addEventListener('click', function () {
                if (radi) return;
                radi = true;
                var stavke = traka.querySelectorAll('.carousel-item-custom');
                traka.insertBefore(stavke[stavke.length - 1], stavke[0]);
                postaviTrenutak(-slot());
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        traka.style.transform = 'translateX(0)';
                        traka.addEventListener('transitionend', function handler(e) {
                            if (e.propertyName !== 'transform') return;
                            traka.removeEventListener('transitionend', handler);
                            radi = false;
                        });
                    });
                });
            });
        }

        window.addEventListener('resize', function () {
            postaviTrenutak(0);
        });
    });


    // dodaj/ukloni iz favorita
    document.querySelectorAll('.btn-favorit').forEach(function (gumb) {
        gumb.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const djeloId = gumb.dataset.djeloId;
            const izvor   = gumb.dataset.izvor;

            fetch(window.BASE_URL + '/ajax/favorit.php', {
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
                    window.location.href = window.BASE_URL + '/prijava.php?poruka=morate_se_prijaviti';
                }
            })
            .catch(function () {
                window.location.href = window.BASE_URL + '/prijava.php';
            });
        });
    });

    // filter po kategoriji
    const filtriDjela = document.getElementById('filtriDjela');
    if (filtriDjela) {
        const karozelDjela = document.getElementById('karozelDjela');
        const filterTraka  = karozelDjela ? karozelDjela.querySelector('.carousel-traka') : null;
        const btnLijevi    = karozelDjela ? karozelDjela.querySelector('.carousel-gumb.lijevi') : null;
        const btnDesni     = karozelDjela ? karozelDjela.querySelector('.carousel-gumb.desni')  : null;
        
        const sveStavke = filterTraka
            ? Array.from(filterTraka.querySelectorAll('.carousel-item-custom'))
            : [];

        filtriDjela.querySelectorAll('.filter-gumb').forEach(function (gumb) {
            gumb.addEventListener('click', function () {
                filtriDjela.querySelectorAll('.filter-gumb').forEach(g => g.classList.remove('aktivan'));
                this.classList.add('aktivan');

                const odabraKateg = this.dataset.kategorija;
                if (!filterTraka) return;

                filterTraka.style.transition = 'none';
                filterTraka.style.transform  = 'translateX(0)';
                void filterTraka.offsetHeight;

                while (filterTraka.firstChild) filterTraka.removeChild(filterTraka.firstChild);
                sveStavke.forEach(function (stavka) {
                    const kateg = (stavka.dataset.kategorija || '').toLowerCase();
                    if (odabraKateg === 'sve' || kateg === odabraKateg.toLowerCase()) {
                        filterTraka.appendChild(stavka);
                    }
                });

                requestAnimationFrame(function () {
                    filterTraka.style.transform = 'translateX(0)';
                    void filterTraka.offsetHeight;
                    filterTraka.style.transition = '';
                });

                var brStavki = filterTraka.querySelectorAll('.carousel-item-custom').length;
                if (btnLijevi) btnLijevi.style.display = brStavki > 3 ? '' : 'none';
                if (btnDesni)  btnDesni.style.display  = brStavki > 3 ? '' : 'none';
            });
        });
    }


    // potvrda brisanja
    document.querySelectorAll('.btn-brisi').forEach(function (gumb) {
        gumb.addEventListener('click', function (e) {
            const naziv = gumb.dataset.naziv || 'ovaj zapis';
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
                const reader = new FileReader();
                reader.onload = function (e) {
                    pregledSlike.src = e.target.result;
                    pregledSlike.style.display = 'block';
                };
                reader.readAsDataURL(datoteka);
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


    // scroll animacije
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('vidljivo');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -48px 0px' });

        document.querySelectorAll('.animiraj').forEach(function (el) {
            observer.observe(el);
        });
    } else {
        document.querySelectorAll('.animiraj').forEach(function (el) {
            el.classList.add('vidljivo');
        });
    }

});
