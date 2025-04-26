// دالة لعرض القسم المختار
function showSection(sectionId) {
    // إخفاء جميع الأقسام أولاً
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => {
        section.classList.add('hidden');
    });

    // إظهار القسم الذي تم اختياره
    const sectionToShow = document.getElementById(sectionId);
    if (sectionToShow) {
        sectionToShow.classList.remove('hidden');
    }
}

// دالة لعرض النماذج داخل قسم Manager
function showManagerForm(formId) {
    // إخفاء جميع النماذج داخل قسم Manager أولاً
    const forms = document.querySelectorAll('#managerSection .form');
    forms.forEach(form => {
        form.classList.add('hidden');
    });

    // إظهار النموذج الذي تم اختياره
    const formToShow = document.getElementById(formId);
    if (formToShow) {
        formToShow.classList.remove('hidden');
    }
}
