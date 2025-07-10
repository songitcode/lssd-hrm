<div class="container">
    <footer class="d-flex flex-column flex-lg-row justify-content-between align-items-start py-3 my-4 border-top">
        <div class="mb-3 mb-lg-0">
            <span class="text-body-secondary">
                © 2025 Designed and developed by <a href="https://github.com/songitcode" target="_blank">@jebsoon</a> -
                version 0.1
            </span>
        </div>
        <div class="mt-2">
            <ul class="contact_admin d-flex justify-content-end">
                <li>
                    <a href="https://github.com/songitcode" target="_blank">
                        <i class="custom_icon fa-brands fa-github"></i>
                    </a>
                </li>
                <li>
                    <a href="https://www.youtube.com/@jason_ngy" target="_blank">
                        <i class="custom_icon fa-brands fa-youtube"></i>
                    </a>
                </li>
                <li>
                    <a href="https://www.linkedin.com/in/nguyenhoangson1606/" target="_blank">
                        <i class="custom_icon fa-brands fa-linkedin"></i>
                    </a>
                </li>
            </ul>
        </div>
    </footer>
</div>
<style>
    .contact_admin {
        gap: 30px;

        .fa-linkedin {
            &:hover {
                color: #0077b5;
            }
        }

        .fa-youtube {
            &:hover {
                color: #ff0033;
            }
        }

        li {
            list-style: none;
            transition: transform 0.3s ease;

            &:hover {
                transform: scale(1.1);
            }
        }
    }

    .custom_icon {
        position: relative;
        font-size: 30px;
        color: #1f2328;

        &::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50px;
            height: 50px;
            transform: translate(-50%, -50%);
            border: 3px solid #e8a800;
            border-radius: 50%;
        }
</style>