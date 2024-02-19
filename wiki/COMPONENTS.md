# Components

These are some examples of available components.

## Data Card Component

```html
<x-oxygen::data.card :title="$pageTitle">
	<x-oxygen::data.row :label="'Membership Number'">{{ $entity->membership_number }}</x-oxygen::data.row>
	<x-oxygen::data.row :label="'Name'">{{ $entity->full_name }}</x-oxygen::data.row>
	<x-oxygen::data.row :label="'Email'">{{ $entity->email }}</x-oxygen::data.row>
	<x-oxygen::data.row :label="'Phone'">{{ $entity->phone }}</x-oxygen::data.row>
</x-oxygen::data.card>
```
