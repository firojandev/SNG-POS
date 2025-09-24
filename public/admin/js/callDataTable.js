$(document).ready(function() {
    // Get current locale from HTML lang attribute
    const currentLocale = document.documentElement.lang || 'en';
    
    // DataTable language configurations
    const dataTableLanguages = {
        'bn': {
            "sSearch": "অনুসন্ধান:",
            "sLengthMenu": "_MENU_ এন্ট্রি দেখান",
            "sInfo": "_START_ থেকে _END_ পর্যন্ত _TOTAL_ এন্ট্রির মধ্যে দেখানো হচ্ছে",
            "sInfoEmpty": "০ থেকে ০ পর্যন্ত ০ এন্ট্রির মধ্যে দেখানো হচ্ছে",
            "sInfoFiltered": "(মোট _MAX_ এন্ট্রি থেকে ফিল্টার করা হয়েছে)",
            "sZeroRecords": "কোন মিলে যাওয়া রেকর্ড পাওয়া যায়নি",
            "sEmptyTable": "টেবিলে কোন ডেটা উপলব্ধ নেই",
            "sLoadingRecords": "লোড হচ্ছে...",
            "sProcessing": "প্রক্রিয়াকরণ...",
            "oPaginate": {
                "sFirst": "প্রথম",
                "sLast": "শেষ",
                "sNext": "পরবর্তী",
                "sPrevious": "পূর্ববর্তী"
            },
            "oAria": {
                "sSortAscending": ": ক্রমবর্ধমান ক্রমে সাজানোর জন্য সক্রিয় করুন",
                "sSortDescending": ": ক্রমহ্রাসমান ক্রমে সাজানোর জন্য সক্রিয় করুন"
            }
        },
        'de': {
            "sSearch": "Suchen:",
            "sLengthMenu": "_MENU_ Einträge anzeigen",
            "sInfo": "_START_ bis _END_ von _TOTAL_ Einträgen",
            "sInfoEmpty": "0 bis 0 von 0 Einträgen",
            "sInfoFiltered": "(gefiltert von _MAX_ Einträgen insgesamt)",
            "sZeroRecords": "Keine übereinstimmenden Aufzeichnungen gefunden",
            "sEmptyTable": "Keine Daten in der Tabelle verfügbar",
            "sLoadingRecords": "Lädt...",
            "sProcessing": "Verarbeitung...",
            "oPaginate": {
                "sFirst": "Erste",
                "sLast": "Letzte",
                "sNext": "Nächste",
                "sPrevious": "Zurück"
            },
            "oAria": {
                "sSortAscending": ": aktivieren, um Spalte aufsteigend zu sortieren",
                "sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
            }
        },
        'fr': {
            "sSearch": "Rechercher:",
            "sLengthMenu": "Afficher _MENU_ entrées",
            "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "sInfoEmpty": "Affichage de 0 à 0 sur 0 entrées",
            "sInfoFiltered": "(filtré à partir de _MAX_ entrées au total)",
            "sZeroRecords": "Aucun enregistrement correspondant trouvé",
            "sEmptyTable": "Aucune donnée disponible dans le tableau",
            "sLoadingRecords": "Chargement...",
            "sProcessing": "Traitement...",
            "oPaginate": {
                "sFirst": "Premier",
                "sLast": "Dernier", 
                "sNext": "Suivant",
                "sPrevious": "Précédent"
            },
            "oAria": {
                "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
            }
        },
        'en': {
            "sSearch": "Search:",
            "sLengthMenu": "Show _MENU_ entries",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
            "sInfoFiltered": "(filtered from _MAX_ total entries)",
            "sZeroRecords": "No matching records found",
            "sEmptyTable": "No data available in table",
            "sLoadingRecords": "Loading...",
            "sProcessing": "Processing...",
            "oPaginate": {
                "sFirst": "First",
                "sLast": "Last",
                "sNext": "Next",
                "sPrevious": "Previous"
            },
            "oAria": {
                "sSortAscending": ": activate to sort column ascending",
                "sSortDescending": ": activate to sort column descending"
            }
        }
    };

    // Get language configuration for current locale, fallback to English
    const languageConfig = dataTableLanguages[currentLocale] || dataTableLanguages['en'];
    
    // Initialize DataTable with localization
    $('.data-table').DataTable({
        "language": languageConfig,
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "order": [[0, "asc"]]
    });
});
