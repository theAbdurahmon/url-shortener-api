<?php
namespace App\Services;

use App\Exceptions\InvalidSlugException;
use App\Services\SlugGenerator;
use App\Repositories\LinkRepository;
use App\Models\Link;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
class LinkService {
    private array $blackList = [
      "api", "admin", "login", "logout", "register",
      "dashboard", "app", "health", "storage",
      "robots.txt", "sitemap.xml", "favicon.ico",
      "stats", "edit", "delete", "qr"
    ];

    public function __construct(
        private LinkRepository $linkRepository,
        private SlugGenerator $slugGenerator
    ){}

    private function checkBlackList($slug): bool {
           return in_array($slug, $this->blackList);
    }

    private function hashPasswordIfExists(array $data): array {
         if(isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
         }
         return $data;
    }
    
    public function getAllData(): Collection{
        return $this->linkRepository->getAllData();
    }
    public function create(array $data): Link {
        $data = $this->hashPasswordIfExists($data);
        $userSlug = $data["slug"] ?? null;

        if($this->checkBlackList($userSlug)) {
            throw new InvalidSlugException("{$userSlug}");
        }else if(!$userSlug) {
           do {
                $slug = $this->slugGenerator->generate();
           }while($this->checkBlackList($slug));
           return $this->linkRepository->create(array_merge($data, ["slug" => $slug]));
        }

        return $this->linkRepository->create($data);
    }

    public function update(array $data, int $idOfLink): Link {
            $data = $this->hashPasswordIfExists($data);
            if(isset($data["slug"]) && $this->checkBlackList($data["slug"])) {
                throw new InvalidSlugException("{$data['slug']}");
            }
            return $this->linkRepository->update($data, $idOfLink);
    }

    public function getLink(int $id): Link {
        return $this->linkRepository->get($id);
    }

    public function delete(int $id): void{
        $this->linkRepository->delete($id);
    }
}