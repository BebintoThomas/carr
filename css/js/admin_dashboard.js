document.addEventListener("DOMContentLoaded", () => {
    // Animate stat cards
    gsap.from(".stat-card", {
        opacity: 0,
        y: 50,
        duration: 1,
        stagger: 0.2,
        ease: "power3.out"
    });

    // Animate table rows
    gsap.from("table tr", {
        opacity: 0,
        x: -50,
        duration: 0.8,
        stagger: 0.1,
        ease: "power2.out",
        delay: 0.5
    });

    // Animate form inputs
    gsap.from(".add-form input, .add-form select, .add-form button", {
        opacity: 0,
        scale: 0.8,
        duration: 0.6,
        stagger: 0.1,
        ease: "back.out(1.7)"
    });
});