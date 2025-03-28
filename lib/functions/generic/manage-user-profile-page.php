<?php 
/**
 * Helper Function to add toggle for WooCommerce customer addresses on User Profile Page. 
 */   

function add_toggle_to_customer_addresses() {
    if ( is_admin() ) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Select only h2 elements whose text contains "address" (case insensitive)
                const headings = Array.from(document.querySelectorAll('h2'))
                    .filter(el => el.textContent.toLowerCase().includes('address'));
                
                headings.forEach(function(heading) {
                    // Create a wrapper to hold the subsequent content until the next <h2>
                    const wrapper = document.createElement('div');

                    // Start with the element following the heading
                    let nextElem = heading.nextElementSibling;
                    
                    // Move elements until the next <h2> is encountered
                    while (nextElem && nextElem.tagName.toLowerCase() !== 'h2') {
                        let temp = nextElem.nextElementSibling;
                        wrapper.appendChild(nextElem);
                        nextElem = temp;
                    }
                    
                    // Insert the wrapper right after the heading
                    heading.parentNode.insertBefore(wrapper, heading.nextSibling);
                    
                    // Hide the wrapper by default
                    wrapper.style.display = 'none';
                    
                    // Make the heading appear interactive and bind the click toggle event
                    heading.style.cursor = 'pointer';
                    heading.addEventListener('click', function() {
                        wrapper.style.display = (wrapper.style.display === 'none') ? 'block' : 'none';
                    });
                });
            });
        </script>
        <style>
            h2 {
                font-weight: bold;
                padding: 10px;
                background: #f1f1f1;
                border: 1px solid #ddd;
                margin-bottom: 0;
            }
        </style>
        <?php
    }
}
if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === 'profile.php') {
    add_action('admin_footer', 'add_toggle_to_customer_addresses');
}
